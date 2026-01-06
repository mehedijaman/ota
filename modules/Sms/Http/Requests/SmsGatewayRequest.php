<?php

namespace Modules\Sms\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SmsGatewayRequest extends FormRequest
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
        $providersConfig = config('sms.providers', []);

        return [
            'name' => ['required', 'string', 'max:255'],
            'provider_class' => ['required', 'string', 'in:'.implode(',', array_keys($providersConfig))],
            'credentials' => ['nullable', 'array'],
            'is_default' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The gateway name is required.',
            'provider_class.required' => 'Please select an SMS provider.',
            'provider_class.in' => 'Invalid SMS provider selected.',
        ];
    }
}
