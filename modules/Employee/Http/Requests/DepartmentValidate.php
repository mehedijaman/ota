<?php

namespace Modules\Employee\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Employee\Models\Department;
use Modules\Support\Http\Requests\Request;

class DepartmentValidate extends Request
{
    public function rules(): array
    {
        $department = $this->route('department');

        return [
            'name' => 'required|min:2|max:255',
            'code' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^\S*$/',
                Rule::unique(Department::class)->ignore($department?->id),
            ],
            'is_active' => 'nullable|boolean',
        ];
    }
}
