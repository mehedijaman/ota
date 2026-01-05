<?php

namespace Modules\Employee\Options;

use Modules\Employee\Models\Area;

class AreaOptions
{
    public static function get(): array
    {
        return Area::orderBy('name')
            ->where('is_active', 1)
            ->get()
            ->map(fn ($area) => [
                'value' => $area->id,
                'label' => trim(($area->code ? $area->code.' - ' : '').$area->name),
            ])
            ->all();
    }

    public static function getValues(): array
    {
        return array_map(fn ($item) => $item['value'], self::get());
    }
}
