<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Document numbering's fast path on Postgres: a native SEQUENCE per
     * document type. nextval() is effectively lock-free and, crucially,
     * NOT transactional — it takes effect immediately and is never rolled
     * back, so it never holds a lock for the lifetime of whatever larger
     * transaction it's called from (unlike the document_sequences table +
     * SELECT ... FOR UPDATE this replaces, which — called from inside an
     * already-open transaction, as every caller does — gets demoted to a
     * savepoint whose lock is held until that OUTER transaction commits).
     * At 50-80 concurrent users that was serializing every purchase/sales
     * order and invoice creation in the app on one lock row.
     *
     * Each sequence starts where document_sequences left off, so already
     * issued numbers are never reissued. See App\Support\DocumentNumber.
     *
     * Non-Postgres connections (sqlite in tests) don't support CREATE
     * SEQUENCE and keep using the document_sequences table as a fallback —
     * DocumentNumber picks the implementation by driver.
     */
    private const TYPES = [
        'purchase_order',
        'sales_order',
        'stock_transfer',
        'stock_adjustment',
        'invoice',
        'payment',
    ];

    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        $startingPoints = DB::table('document_sequences')->pluck('next_number', 'type');

        foreach (self::TYPES as $type) {
            $start = (int) ($startingPoints[$type] ?? 1);
            DB::statement("CREATE SEQUENCE IF NOT EXISTS {$type}_number_seq AS BIGINT START WITH {$start} INCREMENT BY 1");
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        foreach (self::TYPES as $type) {
            DB::statement("DROP SEQUENCE IF EXISTS {$type}_number_seq");
        }
    }
};
