<?php

namespace Modules\Page\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Support\Http\Requests\Request;

class PageValidate extends Request
{
    public function rules(): array
    {
        $pageId = $this->route('id');

        return [
            'title' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('pages', 'slug')->ignore($pageId),
            ],
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'meta_tag_title' => 'nullable|string|max:60',
            'meta_tag_description' => 'nullable|string|max:160',
            'published_at' => 'nullable|date',
            'active' => 'nullable|boolean',
            'remove_previous_image' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'slug.regex' => 'The slug must contain only lowercase letters, numbers, and hyphens.',
            'slug.unique' => 'This slug is already in use by another page.',
        ];
    }
}
