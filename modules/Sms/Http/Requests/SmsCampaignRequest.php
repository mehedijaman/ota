<?php

namespace Modules\Sms\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SmsCampaignRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'template_id' => [
                'nullable',
                Rule::exists('sms_templates', 'id')->where('is_active', true),
            ],
            'message' => ['nullable', 'string', 'max:1000', 'required_without:template_id'],
            'to_phones' => ['nullable', 'string', 'max:100000', 'required_without:numbers_file'],
            'numbers_file' => ['nullable', 'file', 'mimes:csv,txt,xlsx,xls', 'required_without:to_phones'],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The campaign name is required.',
            'message.required_without' => 'Message content is required when no template is selected.',
            'to_phones.required_without' => 'Phone numbers are required when no file is uploaded.',
            'numbers_file.required_without' => 'A file is required when no phone numbers are entered.',
            'scheduled_at.after' => 'The scheduled time must be in the future.',
        ];
    }
}
