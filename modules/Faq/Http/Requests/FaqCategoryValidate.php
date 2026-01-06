<?php

namespace Modules\Faq\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Support\Http\Requests\Request;

class FaqCategoryValidate extends Request
{
    public function rules(): array
    {
        $categoryId = $this->route('faqCategory')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:150',
            ],
            'slug' => [
                'nullable',
                'string',
                'max:150',
                Rule::unique('faq_categories', 'slug')->ignore($categoryId),
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The category name is required.',
            'name.max' => 'The category name must not exceed 150 characters.',
            'slug.unique' => 'This slug is already in use.',
        ];
    }
}
