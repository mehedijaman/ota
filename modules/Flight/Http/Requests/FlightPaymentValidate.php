<?php

namespace Modules\Flight\Http\Requests;

use Modules\Support\Http\Requests\Request;

class FlightPaymentValidate extends Request
{
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:payment,refund'],
            'method' => ['required', 'string', 'in:cash,bank_transfer,credit_card,debit_card,bkash,nagad,rocket,upay,paypal,stripe,cheque,corporate_credit,agent_credit,points,credit_balance'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency_code' => ['nullable', 'string', 'size:3'],

            // Transaction details
            'reference_number' => ['nullable', 'string', 'max:100'],
            'transaction_id' => ['nullable', 'string', 'max:100'],

            // Card details (when applicable)
            'card_type' => ['nullable', 'required_if:method,credit_card,debit_card', 'string', 'in:visa,mastercard,amex'],
            'card_last_four' => ['nullable', 'string', 'size:4'],
            'card_holder_name' => ['nullable', 'string', 'max:100'],

            // Bank transfer details
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_branch' => ['nullable', 'string', 'max:100'],

            // Mobile payment details
            'mobile_number' => ['nullable', 'string', 'max:20'],

            // Cheque details
            'cheque_number' => ['nullable', 'required_if:method,cheque', 'string', 'max:20'],
            'cheque_date' => ['nullable', 'required_if:method,cheque', 'date'],

            // Processing
            'processing_fee' => ['nullable', 'numeric', 'min:0'],
            'gateway_fee' => ['nullable', 'numeric', 'min:0'],

            // Status
            'status' => ['nullable', 'string', 'in:pending,processing,completed,failed,cancelled,refunded'],

            // Refund specific
            'original_payment_id' => ['nullable', 'required_if:type,refund', 'integer', 'exists:flight_booking_payments,id'],
            'refund_reason' => ['nullable', 'string', 'max:500'],

            // Notes
            'notes' => ['nullable', 'string', 'max:500'],
            'collection_notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'method.required' => 'Payment method is required.',
            'method.in' => 'Invalid payment method.',
            'amount.required' => 'Payment amount is required.',
            'amount.min' => 'Payment amount must be greater than zero.',
            'card_type.required_if' => 'Card type is required for card payments.',
            'cheque_number.required_if' => 'Cheque number is required for cheque payments.',
            'cheque_date.required_if' => 'Cheque date is required for cheque payments.',
            'original_payment_id.required_if' => 'Original payment is required for refunds.',
        ];
    }
}
