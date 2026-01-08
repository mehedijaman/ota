<?php

namespace Modules\Page\Http\Requests;

use Modules\Support\Http\Requests\Request;

class PageValidate extends Request
{
    public function rules(): array
    {
        $imageRule = $this->isMethod('POST')
            ? 'nullable|image|max:2048'
            : 'nullable|image|max:2048';

        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => $imageRule,
            'meta_tag_title' => 'nullable|string|max:60',
            'meta_tag_description' => 'nullable|string|max:160',
            'published_at' => 'nullable|date',
            'active' => 'nullable|boolean',
            'remove_previous_image' => 'nullable|boolean',
        ];
    }
}
