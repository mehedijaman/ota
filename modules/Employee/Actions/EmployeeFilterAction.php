<?php

namespace Modules\Employee\Actions;

use App\Filters\StatusFilter;
use Illuminate\Pipeline\Pipeline;
use Modules\Company\Filters\AreaFilter;
use Modules\Company\Filters\DepartmentFilter;
use Modules\Employee\Filters\DesignationFilter;
use Modules\Employee\Filters\EmployeeSelfFilter;
use Modules\Employee\Models\Employee;

class EmployeeFilterAction
{
    public function handle($validated = null)
    {
        return app(Pipeline::class)
            ->send(Employee::query()->with([
                'company',
                'designation',
                'department',
                'areas',
                'section',
                'shift',
            ]))
            ->through([
                new DesignationFilter($validated),
                new DepartmentFilter($validated),
                new AreaFilter($validated),
                new EmployeeSelfFilter($validated),
                new StatusFilter($validated),
            ])
            ->thenReturn();
    }
}
