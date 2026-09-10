<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Generates sequential document numbers such as PO-000001.
 *
 * Backed by the document_sequences table: each call locks that document
 * type's row (SELECT ... FOR UPDATE) and increments it inside a
 * transaction, so concurrent requests can never be handed the same number.
 * Safe to call from within an already-open DB::transaction() — most
 * callers do, since the number is generated alongside creating the record
 * it belongs to.
 */
class DocumentNumber
{
    public static function generate(string $type, string $prefix): string
    {
        $next = DB::transaction(function () use ($type) {
            $sequence = DB::table('document_sequences')
                ->where('type', $type)
                ->lockForUpdate()
                ->first();

            if (! $sequence) {
                throw new RuntimeException("Unknown document sequence type \"{$type}\". Seed it in the document_sequences table first.");
            }

            DB::table('document_sequences')
                ->where('type', $type)
                ->update(['next_number' => $sequence->next_number + 1, 'updated_at' => now()]);

            return $sequence->next_number;
        });

        return sprintf('%s-%06d', $prefix, $next);
    }
}
