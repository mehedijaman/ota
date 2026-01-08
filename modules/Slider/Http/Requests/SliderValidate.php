<?php

namespace Modules\Slider\Http\Requests;

use Modules\Support\Http\Requests\Request;

class SliderValidate extends Request
{
    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|max:2048', // Max size 2MB
            'url' => 'nullable|string',
            'order' => 'nullable|integer',
            'active' => 'nullable|boolean',
        ];
    }
}
