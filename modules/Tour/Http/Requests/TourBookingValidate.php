<?php

namespace Modules\Tour\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Support\Http\Requests\Request;

class TourBookingValidate extends Request
{
    public function rules(): array
    {
        return [
            'tour_id' => ['required', 'exists:tours,id'],
            'travel_date' => ['nullable', 'date'],
            'pricing_id' => ['nullable', 'exists:tour_pricings,id'],
            'adults_count' => ['required', 'integer', 'min:1'],
            'children_count' => ['nullable', 'integer', 'min:0'],
            'infants_count' => ['nullable', 'integer', 'min:0'],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'contact_address' => ['nullable', 'string'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'emergency_phone' => ['nullable', 'string', 'max:20'],
            'special_requests' => ['nullable', 'string'],
            'dietary_requirements' => ['nullable', 'string'],
            'medical_conditions' => ['nullable', 'string'],
            'discount_code' => ['nullable', 'string', 'max:50'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'booking_source' => ['nullable', Rule::in(['website', 'admin', 'agent', 'api'])],
            'internal_notes' => ['nullable', 'string'],

            // Travelers
            'travelers' => ['nullable', 'array'],
            'travelers.*.type' => ['required', Rule::in(['adult', 'child', 'infant'])],
            'travelers.*.title' => ['nullable', 'string', 'max:10'],
            'travelers.*.first_name' => ['required', 'string', 'max:100'],
            'travelers.*.last_name' => ['nullable', 'string', 'max:100'],
            'travelers.*.email' => ['nullable', 'email'],
            'travelers.*.phone' => ['nullable', 'string', 'max:20'],
            'travelers.*.date_of_birth' => ['nullable', 'date'],
            'travelers.*.gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'travelers.*.passport_number' => ['nullable', 'string', 'max:50'],
            'travelers.*.passport_expiry' => ['nullable', 'date'],
            'travelers.*.nid_number' => ['nullable', 'string', 'max:20'],

            // Extras
            'extras' => ['nullable', 'array'],
            'extras.*.extra_id' => ['required', 'exists:tour_extras,id'],
            'extras.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'tour_id.required' => 'Please select a tour.',
            'adults_count.required' => 'At least one adult is required.',
            'contact_name.required' => 'Contact name is required.',
            'contact_email.required' => 'Contact email is required.',
            'travelers.*.first_name.required' => 'Traveler first name is required.',
        ];
    }
}
