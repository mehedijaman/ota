<?php

namespace Modules\Employee\Options;

use Modules\Employee\Models\Designation;

class DesignationOptions
{
    public static function get(): array
    {
        return Designation::orderBy('name')
            ->where('is_active', 1)
            ->get()
            ->map(fn ($designation) => [
                'value' => $designation->id,
                'label' => trim(($designation->code ? $designation->code.' - ' : '').$designation->name),
            ])
            ->all();
    }

    public static function getValues(): array
    {
        // Return only the values for use in other contexts
        return array_map(fn ($item) => $item['value'], self::get());
    }
}
