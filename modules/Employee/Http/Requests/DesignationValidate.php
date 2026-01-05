<?php

namespace Modules\Employee\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Employee\Models\Designation;
use Modules\Support\Http\Requests\Request;

class DesignationValidate extends Request
{
    public function rules(): array
    {
        $designation = $this->route('designation');

        return [
            'name' => 'required|min:3|max:255',
            'code' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^\S*$/', // This regex ensures no spaces, tabs, or newlines
                Rule::unique(Designation::class)->ignore($designation?->id),
            ],
            'is_active' => 'nullable|boolean',
        ];
    }
}
