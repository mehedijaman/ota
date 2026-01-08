<?php

namespace Modules\Menu\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Menu\Services\MenuPositionOptions;
use Modules\Support\Http\Requests\Request;

class MenuValidate extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'position' => [
                'required',
                'string',
                Rule::in(MenuPositionOptions::getValues()),
            ],
            'active' => 'nullable|boolean',
        ];
    }
}
