<?php

namespace Modules\Employee\Options;

class GenderOptions
{
    public static function get(): array
    {
        return [
            [
                'value' => 'Male',
                'label' => 'Male',
            ],
            [
                'value' => 'Female',
                'label' => 'Female',
            ],
        ];
    }

    public static function getValues(): array
    {
        // Return only the values for use in other contexts
        return array_map(fn ($item) => $item['value'], self::get());
    }
}
