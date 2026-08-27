<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DocumentNumberService
{
    /**
     * Build the next running document number for a prefix, e.g. LV202608270003.
     *
     * The sequence is derived from the highest number already issued for the prefix
     * rather than from a row count, so numbers never repeat after a record is deleted.
     * The lookup runs on the raw table and therefore also sees soft-deleted rows.
     *
     * Call this inside the same transaction as the insert it numbers: the row lock is
     * held until that transaction commits, which is what stops two concurrent requests
     * from claiming the same number.
     */
    public static function next(string $prefix, string $table, string $column, int $padding = 4): string
    {
        return DB::transaction(function () use ($prefix, $table, $column, $padding) {
            $latest = DB::table($table)
                ->where($column, 'like', $prefix.'%')
                ->orderByDesc($column)
                ->lockForUpdate()
                ->value($column);

            $sequence = $latest === null
                ? 1
                : ((int) substr((string) $latest, strlen($prefix))) + 1;

            return $prefix.str_pad((string) $sequence, $padding, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Same as {@see self::next()} with the conventional "prefix + today" format.
     */
    public static function nextForToday(string $prefix, string $table, string $column, int $padding = 4): string
    {
        return self::next($prefix.now()->format('Ymd'), $table, $column, $padding);
    }
}
