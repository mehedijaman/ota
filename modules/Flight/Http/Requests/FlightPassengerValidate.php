<?php

namespace Modules\Flight\Http\Requests;

use Modules\Support\Http\Requests\Request;

class FlightPassengerValidate extends Request
{
    public function rules(): array
    {
        return [
            'passenger_number' => ['nullable', 'integer', 'min:1'],
            'passenger_type' => ['required', 'string', 'in:adult,child,infant'],
            'is_lead_passenger' => ['nullable', 'boolean'],

            // Personal Information
            'title' => ['required', 'string', 'in:Mr,Mrs,Ms,Miss,Dr,Master,Mstr'],
            'first_name' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\-]+$/'],
            'middle_name' => ['nullable', 'string', 'max:100', 'regex:/^[a-zA-Z\s\-]+$/'],
            'last_name' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\-]+$/'],
            'gender' => ['required', 'string', 'in:male,female'],
            'date_of_birth' => ['required', 'date', 'before:today'],

            // Nationality & Passport
            'nationality_id' => ['nullable', 'integer', 'exists:countries,id'],
            'passport_number' => ['nullable', 'string', 'max:20'],
            'passport_country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'passport_issue_date' => ['nullable', 'date', 'before:today'],
            'passport_expiry_date' => ['nullable', 'date', 'after:+6 months'],

            // Contact
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],

            // Preferences
            'meal_preference' => ['nullable', 'string', 'in:standard,vegetarian,vegan,halal,kosher,gluten_free,diabetic,child_meal,infant_meal'],
            'seat_preference' => ['nullable', 'string', 'in:window,aisle,middle,any'],

            // Frequent Flyer
            'frequent_flyer_airline' => ['nullable', 'string', 'size:2'],
            'frequent_flyer_number' => ['nullable', 'string', 'max:20'],

            // Special Services
            'requires_wheelchair' => ['nullable', 'boolean'],
            'wheelchair_type' => ['nullable', 'string', 'in:WCHR,WCHS,WCHC'],
            'requires_assistance' => ['nullable', 'boolean'],
            'is_unaccompanied_minor' => ['nullable', 'boolean'],
            'has_medical_condition' => ['nullable', 'boolean'],
            'medical_notes' => ['nullable', 'string', 'max:500'],
            'requires_oxygen' => ['nullable', 'boolean'],
            'traveling_with_pet' => ['nullable', 'boolean'],
            'special_service_requests' => ['nullable', 'array'],

            // Associated Adult (for infants)
            'associated_adult_id' => ['nullable', 'integer', 'exists:flight_booking_passengers,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'first_name.regex' => 'First name must contain only letters, spaces, and hyphens.',
            'last_name.required' => 'Last name is required.',
            'last_name.regex' => 'Last name must contain only letters, spaces, and hyphens.',
            'date_of_birth.required' => 'Date of birth is required.',
            'date_of_birth.before' => 'Date of birth must be before today.',
            'passport_expiry_date.after' => 'Passport must be valid for at least 6 months.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validatePassengerAge($validator);
        });
    }

    protected function validatePassengerAge($validator): void
    {
        $dob = $this->input('date_of_birth');
        $type = $this->input('passenger_type');

        if (! $dob || ! $type) {
            return;
        }

        $age = now()->diffInYears($dob);

        match ($type) {
            'adult' => $age < 12 ? $validator->errors()->add('passenger_type', 'Adults must be 12 years or older.') : null,
            'child' => ($age < 2 || $age > 11) ? $validator->errors()->add('passenger_type', 'Children must be between 2-11 years old.') : null,
            'infant' => $age >= 2 ? $validator->errors()->add('passenger_type', 'Infants must be under 2 years old.') : null,
            default => null,
        };
    }
}
