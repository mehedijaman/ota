<?php

namespace Modules\Visa\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Support\Http\Requests\Request;

class VisaApplicationPaymentValidate extends Request
{
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', Rule::in(['cash', 'card', 'bank_transfer', 'mobile_banking', 'online', 'check', 'other'])],
            'transaction_id' => ['nullable', 'string', 'max:100'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
            'paid_at' => ['nullable', 'date'],
            'status' => ['nullable', Rule::in(['pending', 'completed', 'failed', 'refunded', 'cancelled'])],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Payment amount is required.',
            'amount.min' => 'Payment amount must be greater than zero.',
            'payment_method.required' => 'Payment method is required.',
        ];
    }
}
