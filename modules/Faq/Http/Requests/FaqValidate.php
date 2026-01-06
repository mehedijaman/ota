<?php

namespace Modules\Faq\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Support\Http\Requests\Request;

class FaqValidate extends Request
{
    public function rules(): array
    {
        return [
            'faq_category_id' => [
                'nullable',
                'integer',
                Rule::exists('faq_categories', 'id'),
            ],
            'question' => [
                'required',
                'string',
                'max:500',
            ],
            'answer' => [
                'required',
                'string',
            ],
            'status' => [
                'required',
                Rule::in(['draft', 'published', 'archived']),
            ],
            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'is_featured' => [
                'nullable',
                'boolean',
            ],
            'target_audience' => [
                'nullable',
                Rule::in(['all', 'b2c', 'b2b']),
            ],
            'meta_title' => [
                'nullable',
                'string',
                'max:255',
            ],
            'meta_description' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'question.required' => 'The question is required.',
            'question.max' => 'The question must not exceed 500 characters.',
            'answer.required' => 'The answer is required.',
            'status.required' => 'The status is required.',
            'status.in' => 'The status must be draft, published, or archived.',
            'faq_category_id.exists' => 'The selected category does not exist.',
        ];
    }
}
