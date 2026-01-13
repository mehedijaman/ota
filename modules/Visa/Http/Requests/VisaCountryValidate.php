<?php

namespace Modules\Visa\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Support\Http\Requests\Request;

class VisaCountryValidate extends Request
{
    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:3', Rule::unique('visa_countries', 'code')->ignore($id)],
            'slug' => ['required', 'string', 'max:255', Rule::unique('visa_countries', 'slug')->ignore($id)],
            'continent' => ['nullable', 'string', 'max:50'],
            'capital' => ['nullable', 'string', 'max:100'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'visa_info' => ['nullable', 'string'],
            'embassy_name' => ['nullable', 'string', 'max:255'],
            'embassy_address' => ['nullable', 'string', 'max:500'],
            'embassy_phone' => ['nullable', 'string', 'max:50'],
            'embassy_email' => ['nullable', 'email', 'max:255'],
            'embassy_website' => ['nullable', 'url', 'max:255'],
            'embassy_working_hours' => ['nullable', 'array'],
            'is_popular' => ['boolean'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
            'flag' => ['nullable', 'image', 'max:1024'],
            'banner' => ['nullable', 'image', 'max:2048'],
            'remove_flag' => ['nullable', 'boolean'],
            'remove_banner' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Country name is required.',
            'code.required' => 'Country code is required.',
            'code.unique' => 'This country code is already taken.',
            'slug.required' => 'URL slug is required.',
            'slug.unique' => 'This slug is already taken.',
            'embassy_email.email' => 'Please enter a valid email address.',
            'embassy_website.url' => 'Please enter a valid URL.',
        ];
    }
}
