<?php

namespace Modules\Sms\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SmsSendRequest extends FormRequest
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
            'phone' => ['required', 'string', 'max:30'],
            'message' => ['nullable', 'string', 'max:1000', 'required_without:template_id'],
            'template_id' => [
                'nullable',
                Rule::exists('sms_templates', 'id')->where('is_active', true),
            ],
            'gateway_id' => [
                'nullable',
                Rule::exists('sms_gateways', 'id')->where('is_active', true),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'phone.required' => 'A phone number is required.',
            'message.required_without' => 'Message content is required when no template is selected.',
        ];
    }
}
