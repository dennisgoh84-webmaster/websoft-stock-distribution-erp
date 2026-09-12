<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Same native-sequence fast path as add_native_sequences_for_document_numbers
     * for the two document types this adds (see that migration's docblock
     * for why: nextval() never holds a lock across the outer transaction
     * that document number generation is typically called from). Split
     * into its own migration, run right after the tables that need it
     * exist, rather than folded into that earlier migration, since it's
     * new document types being added later rather than a change to it.
     */
    private const TYPES = ['receipt', 'payment_voucher'];

    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        $startingPoints = DB::table('document_sequences')
            ->whereIn('type', self::TYPES)
            ->pluck('next_number', 'type');

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
