<?php

namespace Modules\Employee\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Support\Http\Requests\Request;

class EmployeeAccountValidate extends Request
{
    public function rules(): array
    {
        return [
            'employee_id' => [
                'required',
                'integer',
                'exists:employees,id',
                Rule::unique('employee_accounts', 'employee_id')->whereNull('deleted_at'),
            ],
            'balance' => 'required|numeric',
        ];
    }
}
