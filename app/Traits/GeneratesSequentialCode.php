<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait GeneratesSequentialCode
{
    public static function generateNextCode(string $prefix = 'P', int $length = 5): string
    {
        return DB::transaction(function () use ($prefix, $length) {
            $lastCode = self::lockForUpdate()->where('code', 'like', $prefix.'%')->orderBy('id', 'desc')->value('code');

            if (! $lastCode) {
                return $prefix.str_pad(1, $length, '0', STR_PAD_LEFT);
            }

            preg_match('/^([A-Z]*)(\d+)$/i', $lastCode, $matches);

            $prefix = $matches[1] ?? $prefix;
            $number = isset($matches[2]) ? (int) $matches[2] : 0;
            $nextNumber = $number + 1;

            return $prefix.str_pad($nextNumber, $length, '0', STR_PAD_LEFT);
        });
    }
}
