<?php

namespace Modules\Flight\Http\Requests;

use Modules\Support\Http\Requests\Request;

class FlightBookingValidate extends Request
{
    public function rules(): array
    {
        return [
            // Basic booking info
            'supplier_id' => ['nullable', 'integer', 'exists:flight_suppliers,id'],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'agent_id' => ['nullable', 'integer', 'exists:agents,id'],
            'trip_type' => ['required', 'string', 'in:one_way,round_trip,multi_city,open_jaw'],
            'departure_date' => ['required', 'date'],
            'return_date' => ['nullable', 'date', 'after:departure_date'],

            // Passenger counts
            'adult_count' => ['required', 'integer', 'min:1', 'max:9'],
            'child_count' => ['nullable', 'integer', 'min:0', 'max:8'],
            'infant_count' => ['nullable', 'integer', 'min:0', 'max:4', 'lte:adult_count'],

            // Contact information
            'contact_title' => ['required', 'string', 'in:Mr,Mrs,Ms,Miss,Dr,Prof'],
            'contact_first_name' => ['required', 'string', 'max:100'],
            'contact_last_name' => ['required', 'string', 'max:100'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone_country_code' => ['required', 'string', 'max:10'],
            'contact_phone' => ['required', 'string', 'max:20'],
            'contact_address' => ['nullable', 'string', 'max:500'],
            'contact_city' => ['nullable', 'string', 'max:100'],
            'contact_country_id' => ['nullable', 'integer', 'exists:countries,id'],

            // Booking options
            'special_requests' => ['nullable', 'string', 'max:1000'],
            'booking_source' => ['nullable', 'string', 'in:website,admin,agent,phone,email,api'],
            'internal_notes' => ['nullable', 'string', 'max:1000'],

            // Segments (flight legs)
            'segments' => ['required', 'array', 'min:1'],
            'segments.*.segment_order' => ['required', 'integer', 'min:1'],
            'segments.*.segment_type' => ['required', 'string', 'in:outbound,inbound,multi_city'],
            'segments.*.airline_id' => ['required', 'integer', 'exists:flight_airlines,id'],
            'segments.*.flight_number' => ['required', 'string', 'max:10'],
            'segments.*.departure_airport_id' => ['required', 'integer', 'exists:flight_airports,id'],
            'segments.*.arrival_airport_id' => ['required', 'integer', 'exists:flight_airports,id', 'different:segments.*.departure_airport_id'],
            'segments.*.departure_datetime' => ['required', 'date'],
            'segments.*.arrival_datetime' => ['required', 'date', 'after:segments.*.departure_datetime'],
            'segments.*.cabin_class' => ['required', 'string', 'in:economy,premium_economy,business,first'],
            'segments.*.booking_class' => ['nullable', 'string', 'size:1'],
            'segments.*.fare_basis' => ['nullable', 'string', 'max:20'],
            'segments.*.baggage_allowance' => ['nullable', 'string', 'max:50'],

            // Passengers
            'passengers' => ['required', 'array', 'min:1'],
            'passengers.*.passenger_type' => ['required', 'string', 'in:adult,child,infant'],
            'passengers.*.title' => ['required', 'string', 'in:Mr,Mrs,Ms,Miss,Dr,Master,Mstr'],
            'passengers.*.first_name' => ['required', 'string', 'max:100'],
            'passengers.*.last_name' => ['required', 'string', 'max:100'],
            'passengers.*.gender' => ['required', 'string', 'in:male,female'],
            'passengers.*.date_of_birth' => ['required', 'date', 'before:today'],
            'passengers.*.nationality_id' => ['nullable', 'integer', 'exists:countries,id'],
            'passengers.*.passport_number' => ['nullable', 'string', 'max:20'],
            'passengers.*.passport_country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'passengers.*.passport_expiry_date' => ['nullable', 'date', 'after:today'],
            'passengers.*.email' => ['nullable', 'email', 'max:255'],
            'passengers.*.phone' => ['nullable', 'string', 'max:20'],
            'passengers.*.meal_preference' => ['nullable', 'string'],
            'passengers.*.seat_preference' => ['nullable', 'string', 'in:window,aisle,middle,any'],
            'passengers.*.frequent_flyer_number' => ['nullable', 'string', 'max:20'],
            'passengers.*.is_lead_passenger' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'contact_first_name.required' => 'Contact first name is required.',
            'contact_last_name.required' => 'Contact last name is required.',
            'contact_email.required' => 'Contact email is required.',
            'contact_phone.required' => 'Contact phone is required.',
            'segments.required' => 'At least one flight segment is required.',
            'segments.*.flight_number.required' => 'Flight number is required for all segments.',
            'segments.*.departure_airport_id.different' => 'Departure and arrival airports must be different.',
            'passengers.required' => 'At least one passenger is required.',
            'passengers.*.first_name.required' => 'Passenger first name is required.',
            'passengers.*.last_name.required' => 'Passenger last name is required.',
            'passengers.*.date_of_birth.required' => 'Passenger date of birth is required.',
            'passengers.*.passport_expiry_date.after' => 'Passport must be valid.',
            'infant_count.lte' => 'Number of infants cannot exceed adults.',
        ];
    }
}
