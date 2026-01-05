<?php

namespace Modules\Employee\Options;

use Modules\Employee\Models\Department;

class DepartmentOptions
{
    public static function get(): array
    {
        return Department::orderBy('name')
            ->where('is_active', 1)
            ->get()
            ->map(fn ($department) => [
                'value' => $department->id,
                'label' => trim(($department->code ? $department->code.' - ' : '').$department->name),
            ])
            ->all();
    }

    public static function getValues(): array
    {
        return array_map(fn ($item) => $item['value'], self::get());
    }
}
