<?php

namespace Modules\Employee\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Employee\Models\Area;
use Modules\Support\Http\Requests\Request;

class AreaValidate extends Request
{
    public function rules(): array
    {
        $area = $this->route('area');

        return [
            'name' => 'required|min:2|max:255',
            'code' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^\S*$/',
                Rule::unique(Area::class)->ignore($area?->id),
            ],
            'is_active' => 'nullable|boolean',
        ];
    }
}
