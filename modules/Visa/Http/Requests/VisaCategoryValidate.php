<?php

namespace Modules\Visa\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Support\Http\Requests\Request;

class VisaCategoryValidate extends Request
{
    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', Rule::unique('visa_categories', 'code')->ignore($id)],
            'slug' => ['required', 'string', 'max:255', Rule::unique('visa_categories', 'slug')->ignore($id)],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Category name is required.',
            'code.required' => 'Category code is required.',
            'code.unique' => 'This category code is already taken.',
            'slug.required' => 'URL slug is required.',
            'slug.unique' => 'This slug is already taken.',
        ];
    }
}
