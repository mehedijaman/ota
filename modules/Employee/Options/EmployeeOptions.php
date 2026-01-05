<?php

namespace Modules\Employee\Options;

use Modules\Employee\Models\Employee;

class EmployeeOptions
{
    public static function get(): array
    {
        return Employee::query()
            ->where('is_active', 1)
            ->orderBy('name')
            ->get()
            ->map(fn ($employee) => [
                'value' => $employee->id,
                'label' => ($employee->code ? $employee->code.' - ' : '').$employee->name,
            ])
            ->all();
    }
}
