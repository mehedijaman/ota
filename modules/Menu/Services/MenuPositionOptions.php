<?php

namespace Modules\Menu\Services;

class MenuPositionOptions
{
    public static function get(): array
    {
        return [
            [
                'value' => 'primary',
                'label' => 'Primary',
            ],
            [
                'value' => 'footer',
                'label' => 'Footer',
            ],
        ];
    }

    public static function getValues(): array
    {
        // Return only the values for use in other contexts
        return array_map(fn ($item) => $item['value'], self::get());
    }
}
