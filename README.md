# Websoft Stock Distribution ERP

A Laravel-based ERP for stock distribution businesses: inventory, purchasing,
sales, warehouse transfers, invoicing, and payments.

## Modules

- **Master data** — categories, units, warehouses, suppliers, customers, products
- **Inventory** — per-warehouse stock levels, stock transfers, stock adjustments, full stock ledger
- **Purchasing** — purchase orders, receiving, auto-generated purchase invoices
- **Sales** — sales orders, fulfillment, auto-generated sales invoices
- **Finance** — invoices (purchase & sales) with balance tracking, payments
- **Access control** — role-based access (Admin, Manager, Staff) via `spatie/laravel-permission`
- **Dashboard** — low-stock alerts, pending orders, outstanding invoices, revenue snapshot

## Getting started

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite   # if using sqlite (default in .env)
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

Default seeded admin login: `admin@example.com` / `password`.

## Tech stack

- Laravel 11
- Blade + Tailwind CSS (Laravel Breeze)
- SQLite by default for local development (swap `DB_CONNECTION` for MySQL/Postgres in production)
- `spatie/laravel-permission` for roles/permissions
