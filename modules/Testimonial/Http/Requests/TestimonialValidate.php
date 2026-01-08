<?php

namespace Modules\Testimonial\Http\Requests;

use Modules\Support\Http\Requests\Request;

class TestimonialValidate extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'remove_previous_image' => 'nullable|boolean',
        ];
    }
}
