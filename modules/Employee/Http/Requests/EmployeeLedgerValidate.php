<?php

namespace Modules\Employee\Http\Requests;

use Modules\Support\Http\Requests\Request;

class EmployeeLedgerValidate extends Request
{
    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'debit' => 'nullable|numeric',
            'credit' => 'nullable|numeric',
            'balance' => 'nullable|numeric',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ];
    }
}
