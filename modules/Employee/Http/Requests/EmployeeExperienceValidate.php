<?php

namespace Modules\Employee\Http\Requests;

use Modules\Support\Http\Requests\Request;

class EmployeeExperienceValidate extends Request
{
    public function rules(): array
    {
        return [
            // 'name' => 'required',
        ];
    }
}
