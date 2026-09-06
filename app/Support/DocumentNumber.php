<?php

namespace App\Support;

/**
 * Generates simple sequential document numbers such as PO-000001.
 *
 * Intended for the scale of a single-warehouse-operator business; it is not
 * safe against concurrent writes racing for the same next number.
 */
class DocumentNumber
{
    public static function generate(string $modelClass, string $prefix): string
    {
        $nextId = ((int) $modelClass::query()->max('id')) + 1;

        return sprintf('%s-%06d', $prefix, $nextId);
    }
}
