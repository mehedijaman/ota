<?php

namespace Modules\Agent\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Agent\Models\Agent;
use Modules\Support\Http\Requests\Request;

class AgentValidate extends Request
{
    public function rules(): array
    {
        $agent = $this->route('agent');

        return [
            'company_name' => 'required|string|max:255',
            'established_at' => 'nullable|date',
            'contact_person' => 'required|string|max:255',

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique(Agent::class)->ignore($agent?->id),
            ],

            'phone' => 'nullable|string|max:50',

            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',

            'trade_license_no' => 'nullable|string|max:100',
            'mocat_no' => 'nullable|string|max:100',
            'nid_no' => 'nullable|string|max:100',
            'tin_no' => 'nullable|string|max:100',

            'status' => 'nullable|in:pending,approved,suspended,inactive',
            'remarks' => 'nullable|string',

            'balance' => 'nullable|numeric|min:0',
            'credit_limit' => 'nullable|numeric|min:0',

            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'remove_logo' => 'nullable|boolean',

            'trade_license' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'remove_trade_license' => 'nullable|boolean',

            'mocat_certificate' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'remove_mocat_certificate' => 'nullable|boolean',

            'tin_certificate' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'remove_tin_certificate' => 'nullable|boolean',

            'nid_front' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'remove_nid_front' => 'nullable|boolean',

            'nid_back' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'remove_nid_back' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => 'The company name is required.',
            'contact_person.required' => 'The contact person name is required.',
            'email.required' => 'The email address is required.',
            'email.unique' => 'This email address is already registered.',
            'logo.image' => 'The logo must be an image.',
            'logo.max' => 'The logo must not exceed 2MB.',
            'trade_license.max' => 'The trade license file must not exceed 5MB.',
            'mocat_certificate.max' => 'The MOCAT certificate file must not exceed 5MB.',
            'tin_certificate.max' => 'The TIN certificate file must not exceed 5MB.',
            'nid_front.max' => 'The NID front file must not exceed 5MB.',
            'nid_back.max' => 'The NID back file must not exceed 5MB.',
        ];
    }
}
