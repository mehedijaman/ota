<?php

namespace Modules\Employee\Options;

class BloodGroupOptions
{
    public static function get(): array
    {
        return [
            [
                'value' => 'A+',
                'label' => 'A+',
            ],
            [
                'value' => 'A-',
                'label' => 'A-',
            ],
            [
                'value' => 'B+',
                'label' => 'B+',
            ],
            [
                'value' => 'B-',
                'label' => 'B-',
            ],
            [
                'value' => 'O+',
                'label' => 'O+',
            ],
            [
                'value' => 'O-',
                'label' => 'O-',
            ],
            [
                'value' => 'AB+',
                'label' => 'AB+',
            ],
            [
                'value' => 'AB-',
                'label' => 'AB-',
            ],
        ];
    }

    public static function getValues(): array
    {
        // Return only the values for use in other contexts
        return array_map(fn ($item) => $item['value'], self::get());
    }
}
