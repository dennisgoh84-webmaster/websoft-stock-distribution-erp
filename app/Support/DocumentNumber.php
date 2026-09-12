<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Generates sequential document numbers such as PO-000001.
 *
 * Two implementations, picked by DB driver:
 *
 * - Postgres (production): a native SEQUENCE per type. nextval() is
 *   effectively lock-free and non-transactional — it takes effect
 *   immediately and is never rolled back — so it stays fast and
 *   non-blocking no matter how deep inside a larger transaction it's
 *   called from, which matters here: invoice number generation happens
 *   while a purchase/sales order's row is already locked for
 *   receiving/fulfilling. Numbers can have small gaps if the transaction
 *   that allocated one later rolls back — expected and harmless, every
 *   real accounting system has this property.
 *
 * - Anything else (sqlite in tests): a locked document_sequences row,
 *   incremented inside a transaction. Correct everywhere but the lock is
 *   held until the OUTERMOST enclosing transaction commits if called from
 *   inside one — call it as early as possible in that case.
 */
class DocumentNumber
{
    public const TYPES = [
        'purchase_order',
        'sales_order',
        'stock_transfer',
        'stock_adjustment',
        'invoice',
        'payment',
        'receipt',
        'payment_voucher',
    ];

    public static function generate(string $type, string $prefix): string
    {
        if (! in_array($type, self::TYPES, true)) {
            throw new RuntimeException("Unknown document sequence type \"{$type}\".");
        }

        $next = DB::connection()->getDriverName() === 'pgsql'
            ? self::nextFromPostgresSequence($type)
            : self::nextFromLockedTable($type);

        return sprintf('%s-%06d', $prefix, $next);
    }

    private static function nextFromPostgresSequence(string $type): int
    {
        // $type is validated against the TYPES whitelist above, never
        // request input, so interpolating it into the sequence name here
        // is safe.
        return (int) DB::selectOne("SELECT nextval('{$type}_number_seq') AS value")->value;
    }

    private static function nextFromLockedTable(string $type): int
    {
        return DB::transaction(function () use ($type) {
            $sequence = DB::table('document_sequences')
                ->where('type', $type)
                ->lockForUpdate()
                ->first();

            if (! $sequence) {
                throw new RuntimeException("Document sequence \"{$type}\" has not been seeded.");
            }

            DB::table('document_sequences')
                ->where('type', $type)
                ->update(['next_number' => $sequence->next_number + 1, 'updated_at' => now()]);

            return $sequence->next_number;
        });
    }
}
