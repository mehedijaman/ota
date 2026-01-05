<?php

namespace Modules\Employee\Options;

class ReligionOptions
{
    public static function get(): array
    {
        return [
            [
                'value' => 'Islam',
                'label' => 'Islam',
            ],
            [
                'value' => 'Christianity',
                'label' => 'Christianity',
            ],
            [
                'value' => 'Buddhism',
                'label' => 'Buddhism',
            ],
            [
                'value' => 'Hinduism',
                'label' => 'Hinduism',
            ],
        ];
    }

    public static function getValues(): array
    {
        // Return only the values for use in other contexts
        return array_map(fn ($item) => $item['value'], self::get());
    }
}
