<?php

namespace Modules\Customer\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Customer\Models\Customer;
use Modules\Support\Http\Requests\Request;

class CustomerValidate extends Request
{
    public function rules(): array
    {
        $customer = $this->route('customer');

        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique(Customer::class)->ignore($customer?->id),
            ],

            'phone' => 'nullable|string|max:50',

            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',

            'nid_no' => 'nullable|string|max:100',
            'passport_no' => 'nullable|string|max:100',
            'passport_expiry' => 'nullable|date',

            'status' => 'nullable|in:active,inactive,suspended,blocked',

            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'remove_avatar' => 'nullable|boolean',

            'nid_front' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'remove_nid_front' => 'nullable|boolean',

            'nid_back' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'remove_nid_back' => 'nullable|boolean',

            'passport' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'remove_passport' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'The first name is required.',
            'last_name.required' => 'The last name is required.',
            'email.required' => 'The email address is required.',
            'email.unique' => 'This email address is already registered.',
            'avatar.image' => 'The avatar must be an image.',
            'avatar.max' => 'The avatar must not exceed 2MB.',
            'nid_front.max' => 'The NID front file must not exceed 5MB.',
            'nid_back.max' => 'The NID back file must not exceed 5MB.',
            'passport.max' => 'The passport file must not exceed 5MB.',
        ];
    }
}
