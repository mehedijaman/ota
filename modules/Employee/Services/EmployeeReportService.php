<?php

namespace Modules\Employee\Services;

use Modules\Employee\Models\Employee;

class EmployeeReportService
{
    public static function getList()
    {
        $employees = Employee::query()
            ->with(['designation', 'department', 'area'])
            ->when(request('designation'), fn ($q) => $q->where('designation_id', request('designation')))
            ->when(request('department'), fn ($q) => $q->where('department_id', request('department')))
            ->when(request('area'), fn ($q) => $q->where('area_id', request('area')))
            ->when(request()->has('active'), fn ($q) => $q->where('is_active', request('active')))
            ->orderBy('name')
            ->get();

        return $employees->map(function ($employee) {
            return [
                'id' => $employee->id,
                'code' => $employee->code,
                'name' => $employee->name,
                'department' => $employee->department?->name,
                'designation' => $employee->designation?->name,
                'area' => $employee->area?->name,
                'type' => $employee->type,
                'joining_date' => $employee->joining_date,
                'email' => $employee->email,
                'phone' => $employee->phone,
                'is_active' => $employee->is_active,
            ];
        });
    }
}
