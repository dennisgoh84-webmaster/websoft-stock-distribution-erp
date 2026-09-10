# Websoft Stock Distribution ERP

A Laravel-based ERP for stock distribution businesses: inventory, purchasing,
sales, warehouse transfers, invoicing, and payments. Built for concurrent
multi-user use (50-80 concurrent users) — see **Concurrency** below.

## Modules

- **Master data** — categories, units, warehouses, suppliers, customers, products
- **Inventory** — per-warehouse stock levels, stock transfers, stock adjustments, full stock ledger
- **Purchasing** — purchase orders, receiving, auto-generated purchase invoices
- **Sales** — sales orders, fulfillment, auto-generated sales invoices
- **Finance** — invoices (purchase & sales) with balance tracking, payments
- **Access control** — role-based access (Admin, Manager, Staff) via `spatie/laravel-permission`
- **Dashboard** — low-stock alerts, pending orders, outstanding invoices, revenue snapshot

## Getting started

Requires a running PostgreSQL instance (see **Concurrency** for why).

```bash
composer install
cp .env.example .env
php artisan key:generate
# create the database, e.g.:
#   createuser websoft --pwprompt
#   createdb websoft_erp -O websoft
# then set DB_* in .env to match
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

Default seeded admin login: `admin@example.com` / `password`.

For a quick local trial without Postgres, set `DB_CONNECTION=sqlite` and
`touch database/database.sqlite` — but see below before using that for
anything with more than one user at a time.

## Tech stack

- Laravel 11
- Blade + Tailwind CSS (Laravel Breeze)
- PostgreSQL (production target)
- `spatie/laravel-permission` for roles/permissions

## Concurrency

This app is sized for **50-80 concurrent users**, which shapes two decisions:

**Database: PostgreSQL, not SQLite.** SQLite allows only one writer at a
time for the whole database file; at this user count that serializes every
order, receipt, and payment across all users and becomes a bottleneck (and,
depending on journal mode, a source of `database is locked` errors). The app
still runs on SQLite for a single-user local trial and for the test suite
(fast, in-memory, no server needed), but `config/database.php` defaults to
`pgsql` and production should stay on Postgres (or MySQL, which is a drop-in
`.env` swap since nothing here uses Postgres-only SQL).

**Every write that reads-then-writes shared state is row-locked.** Three
places in this codebase update a number based on its current value, which is
unsafe under concurrent requests unless serialized:

- `App\Services\StockService::move()` — every stock quantity change (PO
  receipt, SO fulfillment, transfer, adjustment) locks the specific
  product+warehouse balance row (`SELECT ... FOR UPDATE`) before reading and
  updating it, so two concurrent movements against the same product/warehouse
  can't lose one of the updates or push stock negative.
- `App\Support\DocumentNumber::generate()` — PO/SO/invoice/payment/transfer/
  adjustment numbers no longer come from `max(id)+1` (see **Invoice
  generation speed** below for why locking alone wasn't the right fix here).
- `App\Services\PurchaseOrderService`, `SalesOrderService`, and
  `InvoiceService::recordPayment()` lock the parent order/invoice row for
  the duration of the operation, so concurrent receive/fulfill/pay calls
  against the *same* order or invoice are serialized instead of racing on
  stale status or balance reads (the classic case: two people submitting the
  invoice's payment form at once must never be able to jointly overpay it).

These were verified against a real Postgres instance with genuinely
concurrent OS processes (not just single-threaded tests) hammering the same
rows — 60 parallel stock decrements against one product/warehouse produced
zero lost updates, 80 parallel document-number requests produced 80 unique
numbers, and 20 parallel $10 payments against a $100 invoice balance
produced exactly 10 successes and 10 correctly-rejected overpayment attempts.

Everything else in the write path (incrementing `received_quantity` /
`fulfilled_quantity` on order line items) uses a single atomic
`UPDATE ... SET col = col + ?` via Eloquent's `increment()`, which is already
race-safe at the SQL level without needing a lock.

## Invoice generation speed

Invoice generation (auto-triggered when a purchase order is fully received or
a sales order is fully fulfilled) had three concrete speed problems, found
and fixed together since the first one is subtle enough to be worth
explaining:

**A lock that outlived the operation it was protecting.** The first version
of `DocumentNumber::generate()` fixed correctness (no duplicate numbers) by
locking a counter row for the duration of a transaction — but every caller
generates the number *inside* a larger transaction it doesn't control (e.g.
receiving a PO locks the PO row, moves stock, increments line items, updates
status, *then* generates the invoice number — all in one transaction). On
Postgres/MySQL, a `DB::transaction()` opened from inside another one becomes
a savepoint: its locks aren't released when that inner block finishes, they're
held until the *outermost* transaction commits. So the counter row's lock was
effectively held for the entire receive/fulfill/pay operation, not just the
tiny increment — serializing invoice generation across the app at 50-80
concurrent users, the opposite of the goal.

The fix: on Postgres (the production target), `DocumentNumber` now pulls
numbers from a native `SEQUENCE` per document type. `nextval()` is
non-transactional by design — it takes effect immediately and is never held
or rolled back — so it stays fast no matter how deeply nested the call is.
Numbers can have small gaps if a transaction that allocated one later rolls
back, which is normal and expected for document numbering (every real
accounting system has this property) and a good trade for removing the lock
entirely. A locked-table fallback remains for non-Postgres connections
(sqlite in tests).

Proven, not just reasoned about: 30 concurrent transactions each doing
"generate a number, then ~200ms of other work" — the same shape as a real
receive/fulfill call — took **6.36s** (matching 30 × 0.2s serialized) against
the old locked-row approach, and **0.62s** (effectively parallel) against the
sequence-based fix, isolated at the SQL level with `psql` to rule out any
PHP-side noise.

**Two ordinary N+1/per-row costs**, also fixed:

- `PurchaseOrderService::receive()` / `SalesOrderService::fulfill()` were
  calling `->refresh()->load('items')` right before invoice generation,
  which drops the previously-loaded `product` relation — so
  `InvoiceService` lazy-loaded the product once per line item. Now
  `load('items.product')`.
- Invoice/stock-transfer/stock-adjustment line items were inserted one
  `INSERT` per row in a loop. Now a single bulk `insert()` per document,
  regardless of how many lines it has.

The 4 call sites that don't need the number until later in a bigger flow
(PO/SO/transfer/adjustment creation) also now generate their document number
*before* opening the record-creation transaction, so even the locked-table
fallback path only ever holds its lock for the increment itself.
