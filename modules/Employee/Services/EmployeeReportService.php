<?php

namespace Modules\Employee\Services;

use Modules\Employee\Models\Employee;

class EmployeeReportService
{
    public static function getList()
    {
        $employees = Employee::filter()->get();

        return $employees->map(function ($employee) {
            return [
                'id' => $employee->id,
                'code' => $employee->code,
                'name' => $employee->name,
                'company' => $employee->company,
                'department' => $employee->department,
                'designation' => $employee->designation,
                'area' => $employee->area,
                'shift' => $employee->shift,
                'section' => $employee->section,
                'type' => $employee->type,
                'joining_date' => $employee->joining_date,
                'rfid_no' => $employee->rfid_no,
                'email' => $employee->email,
                'phone' => $employee->phone,
                'active' => $employee->active,
            ];
        });
    }
}
