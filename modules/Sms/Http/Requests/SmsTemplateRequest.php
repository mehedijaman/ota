<?php

namespace Modules\Sms\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SmsTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $templateId = $this->route('smsTemplate')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                $templateId
                    ? 'unique:sms_templates,name,'.$templateId
                    : 'unique:sms_templates,name',
            ],
            'content' => ['required', 'string', 'max:1000'],
            'category' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The template name is required.',
            'name.unique' => 'A template with this name already exists.',
            'content.required' => 'The message content is required.',
            'content.max' => 'The message content cannot exceed 1000 characters.',
        ];
    }
}
