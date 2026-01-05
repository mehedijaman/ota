<?php

namespace Modules\Employee\Http\Requests;

use Modules\Support\Http\Requests\Request;

class EmployeeBankAccountValidate extends Request
{
    public function rules(): array
    {
        return [
            'bank_name' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'account_no' => 'nullable|string|max:255',
            'routing_no' => 'nullable|string|max:255',
            'account_type' => 'nullable|string|max:255',
        ];
    }
}
