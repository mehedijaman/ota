<?php

namespace Modules\Testimonial\Http\Requests;

use Modules\Support\Http\Requests\Request;

class TestimonialValidate extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|max:2048', // Max size 2MB
            'featured' => 'nullable|boolean',
            'active' => 'nullable|boolean',
        ];
    }
}
