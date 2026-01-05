<?php

namespace App\Options;

class StatusOptions
{
    public static function get(): array
    {
        return [
            [
                'value' => 1,
                'label' => 'Active',
            ],
            [
                'value' => 0,
                'label' => 'Inactive',
            ],
        ];
    }

    public static function getValues(): array
    {
        // Return only the values for use in other contexts
        return array_map(fn ($item) => $item['value'], self::get());
    }
}
