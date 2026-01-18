<?php

namespace Modules\Flight\Http\Requests;

use Modules\Support\Http\Requests\Request;

class FlightSearchValidate extends Request
{
    public function rules(): array
    {
        return [
            'trip_type' => ['required', 'string', 'in:one_way,round_trip,multi_city'],
            'origin' => ['required', 'string', 'size:3', 'different:destination'],
            'destination' => ['required', 'string', 'size:3'],
            'departure_date' => ['required', 'date', 'after_or_equal:today'],
            'return_date' => ['nullable', 'required_if:trip_type,round_trip', 'date', 'after:departure_date'],
            'adults' => ['required', 'integer', 'min:1', 'max:9'],
            'children' => ['nullable', 'integer', 'min:0', 'max:8'],
            'infants' => ['nullable', 'integer', 'min:0', 'max:4', 'lte:adults'],
            'child_ages' => ['nullable', 'array'],
            'child_ages.*' => ['integer', 'min:2', 'max:11'],
            'cabin_class' => ['nullable', 'string', 'in:economy,premium_economy,business,first'],
            'direct_only' => ['nullable', 'boolean'],
            'flexible_dates' => ['nullable', 'boolean'],
            'preferred_airline' => ['nullable', 'string', 'size:2'],
            'max_stops' => ['nullable', 'integer', 'min:0', 'max:3'],
        ];
    }

    public function messages(): array
    {
        return [
            'origin.required' => 'Please select departure airport.',
            'origin.size' => 'Invalid airport code.',
            'origin.different' => 'Departure and arrival airports must be different.',
            'destination.required' => 'Please select arrival airport.',
            'destination.size' => 'Invalid airport code.',
            'departure_date.required' => 'Please select departure date.',
            'departure_date.after_or_equal' => 'Departure date cannot be in the past.',
            'return_date.required_if' => 'Return date is required for round trip.',
            'return_date.after' => 'Return date must be after departure date.',
            'adults.required' => 'At least 1 adult is required.',
            'adults.min' => 'At least 1 adult is required.',
            'adults.max' => 'Maximum 9 adults allowed.',
            'infants.lte' => 'Number of infants cannot exceed adults.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'origin' => strtoupper($this->origin ?? ''),
            'destination' => strtoupper($this->destination ?? ''),
            'adults' => (int) ($this->adults ?? 1),
            'children' => (int) ($this->children ?? 0),
            'infants' => (int) ($this->infants ?? 0),
        ]);
    }
}
