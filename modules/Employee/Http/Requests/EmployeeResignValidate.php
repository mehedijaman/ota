<?php

namespace Modules\Employee\Http\Requests;

use Modules\Support\Http\Requests\Request;

class EmployeeResignValidate extends Request
{
    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'reason' => 'required|string|max:255',
            'date' => 'required|date',
            'type' => 'required|string|max:255',
        ];
    }
}
