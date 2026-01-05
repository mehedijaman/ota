<?php

namespace Modules\Employee\Options;

class MaritalStatusOptions
{
    public static function get(): array
    {
        return [
            [
                'value' => 'Single',
                'label' => 'Single',
            ],
            [
                'value' => 'Married',
                'label' => 'Married',
            ],
            [
                'value' => 'Divorced',
                'label' => 'Divorced',
            ],
            [
                'value' => 'Widowed',
                'label' => 'Widowed',
            ],
        ];
    }

    public static function getValues(): array
    {
        // Return only the values for use in other contexts
        return array_map(fn ($item) => $item['value'], self::get());
    }
}
