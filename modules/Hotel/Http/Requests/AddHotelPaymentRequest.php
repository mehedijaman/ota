<?php

namespace Modules\Hotel\Http\Requests;

use Modules\Support\Http\Requests\Request;

class AddHotelPaymentRequest extends Request
{
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'string', 'in:bkash,nagad,rocket,card,bank,cash,check,online'],
            'type' => ['required', 'string', 'in:deposit,partial,full,additional,damage_deposit'],
            'status' => ['required', 'string', 'in:pending,completed,failed'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],

            // Bank transfer details
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_branch' => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_deposit_date' => ['nullable', 'date'],

            // Mobile payment details
            'sender_number' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Payment amount is required.',
            'amount.min' => 'Payment amount must be at least 1.',
            'payment_method.required' => 'Payment method is required.',
            'type.required' => 'Payment type is required.',
            'status.required' => 'Payment status is required.',
        ];
    }
}
