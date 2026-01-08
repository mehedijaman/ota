<?php

namespace Modules\Slider\Http\Requests;

use Modules\Support\Http\Requests\Request;

class SliderValidate extends Request
{
    public function rules(): array
    {
        $imageRule = $this->isMethod('POST')
            ? 'required|image|max:2048'
            : 'nullable|image|max:2048';

        return [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => $imageRule,
            'url' => 'nullable|string|url',
            'button_text' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'remove_previous_image' => 'nullable|boolean',
        ];
    }
}
