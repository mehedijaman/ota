<?php

namespace Modules\Visa\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Support\Http\Requests\Request;

class VisaApplicationValidate extends Request
{
    public function rules(): array
    {
        return [
            'visa_id' => ['required', 'exists:visas,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'agent_id' => ['nullable', 'exists:agents,id'],
            'adult_count' => ['required', 'integer', 'min:1'],
            'child_count' => ['nullable', 'integer', 'min:0'],
            'infant_count' => ['nullable', 'integer', 'min:0'],
            'intended_travel_date' => ['nullable', 'date', 'after_or_equal:today'],
            'intended_return_date' => ['nullable', 'date', 'after:intended_travel_date'],
            'travel_purpose' => ['nullable', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'contact_address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:50'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:50'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'express_fees' => ['nullable', 'numeric', 'min:0'],
            'additional_fees' => ['nullable', 'numeric', 'min:0'],
            'internal_notes' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in([
                'draft', 'pending', 'documents_pending', 'documents_submitted',
                'under_review', 'processing', 'submitted_to_embassy', 'interview_scheduled',
                'approved', 'rejected', 'on_hold', 'cancelled', 'refunded', 'completed',
            ])],
            'status_note' => ['nullable', 'string'],

            // Travelers
            'travelers' => ['required', 'array', 'min:1'],
            'travelers.*.type' => ['required', Rule::in(['adult', 'child', 'infant'])],
            'travelers.*.title' => ['nullable', Rule::in(['Mr', 'Mrs', 'Ms', 'Miss', 'Dr', 'Prof'])],
            'travelers.*.first_name' => ['required', 'string', 'max:100'],
            'travelers.*.last_name' => ['required', 'string', 'max:100'],
            'travelers.*.email' => ['nullable', 'email', 'max:255'],
            'travelers.*.phone' => ['nullable', 'string', 'max:50'],
            'travelers.*.date_of_birth' => ['required', 'date', 'before:today'],
            'travelers.*.gender' => ['required', Rule::in(['male', 'female', 'other'])],
            'travelers.*.marital_status' => ['nullable', Rule::in(['single', 'married', 'divorced', 'widowed', 'separated'])],
            'travelers.*.place_of_birth' => ['nullable', 'string', 'max:100'],
            'travelers.*.country_of_birth' => ['nullable', 'string', 'max:100'],
            'travelers.*.nationality' => ['required', 'string', 'max:100'],
            'travelers.*.passport_number' => ['required', 'string', 'max:50'],
            'travelers.*.passport_type' => ['nullable', 'string', 'max:50'],
            'travelers.*.passport_issue_date' => ['required', 'date', 'before:today'],
            'travelers.*.passport_expiry_date' => ['required', 'date', 'after:today'],
            'travelers.*.passport_issue_place' => ['nullable', 'string', 'max:100'],
            'travelers.*.passport_issue_authority' => ['nullable', 'string', 'max:255'],
            'travelers.*.nid_number' => ['nullable', 'string', 'max:50'],
            'travelers.*.present_address' => ['nullable', 'string', 'max:500'],
            'travelers.*.present_city' => ['nullable', 'string', 'max:100'],
            'travelers.*.present_postal_code' => ['nullable', 'string', 'max:20'],
            'travelers.*.present_country' => ['nullable', 'string', 'max:100'],
            'travelers.*.permanent_address' => ['nullable', 'string', 'max:500'],
            'travelers.*.permanent_city' => ['nullable', 'string', 'max:100'],
            'travelers.*.permanent_postal_code' => ['nullable', 'string', 'max:20'],
            'travelers.*.permanent_country' => ['nullable', 'string', 'max:100'],
            'travelers.*.employment_status' => ['nullable', Rule::in(['employed', 'self_employed', 'unemployed', 'student', 'retired', 'homemaker', 'minor'])],
            'travelers.*.occupation' => ['nullable', 'string', 'max:100'],
            'travelers.*.employer_name' => ['nullable', 'string', 'max:255'],
            'travelers.*.employer_address' => ['nullable', 'string', 'max:500'],
            'travelers.*.employer_phone' => ['nullable', 'string', 'max:50'],
            'travelers.*.job_title' => ['nullable', 'string', 'max:100'],
            'travelers.*.monthly_income' => ['nullable', 'numeric', 'min:0'],
            'travelers.*.father_name' => ['nullable', 'string', 'max:255'],
            'travelers.*.mother_name' => ['nullable', 'string', 'max:255'],
            'travelers.*.spouse_name' => ['nullable', 'string', 'max:255'],
            'travelers.*.has_previous_visa' => ['boolean'],
            'travelers.*.previous_visa_details' => ['nullable', 'string'],
            'travelers.*.has_visa_refusal' => ['boolean'],
            'travelers.*.visa_refusal_details' => ['nullable', 'string'],
            'travelers.*.has_deportation_history' => ['boolean'],
            'travelers.*.deportation_details' => ['nullable', 'string'],
            'travelers.*.has_criminal_record' => ['boolean'],
            'travelers.*.criminal_record_details' => ['nullable', 'string'],
            'travelers.*.special_requests' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'visa_id.required' => 'Please select a visa package.',
            'adult_count.required' => 'Number of adults is required.',
            'adult_count.min' => 'At least one adult is required.',
            'contact_name.required' => 'Contact name is required.',
            'contact_email.required' => 'Contact email is required.',
            'contact_email.email' => 'Please enter a valid email address.',
            'contact_phone.required' => 'Contact phone is required.',
            'travelers.required' => 'At least one traveler is required.',
            'travelers.*.first_name.required' => 'Traveler first name is required.',
            'travelers.*.last_name.required' => 'Traveler last name is required.',
            'travelers.*.date_of_birth.required' => 'Traveler date of birth is required.',
            'travelers.*.gender.required' => 'Traveler gender is required.',
            'travelers.*.nationality.required' => 'Traveler nationality is required.',
            'travelers.*.passport_number.required' => 'Passport number is required.',
            'travelers.*.passport_issue_date.required' => 'Passport issue date is required.',
            'travelers.*.passport_expiry_date.required' => 'Passport expiry date is required.',
            'travelers.*.passport_expiry_date.after' => 'Passport must not be expired.',
        ];
    }
}
