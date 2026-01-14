<?php

namespace Modules\Hotel\Http\Requests;

use Modules\Support\Http\Requests\Request;

class HotelBookingValidate extends Request
{
    public function rules(): array
    {
        return [
            'hotel_id' => ['required', 'integer', 'exists:hotels,id'],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'agent_id' => ['nullable', 'integer', 'exists:agents,id'],
            'check_in_date' => ['required', 'date', 'after_or_equal:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'total_rooms' => ['required', 'integer', 'min:1', 'max:20'],
            'total_adults' => ['required', 'integer', 'min:1'],
            'total_children' => ['nullable', 'integer', 'min:0'],
            'total_infants' => ['nullable', 'integer', 'min:0'],
            'contact_title' => ['nullable', 'string', 'in:Mr,Mrs,Ms,Dr,Prof'],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:50'],
            'contact_address' => ['nullable', 'string', 'max:500'],
            'contact_city' => ['nullable', 'string', 'max:100'],
            'contact_postal_code' => ['nullable', 'string', 'max:20'],
            'contact_country' => ['nullable', 'string', 'max:100'],
            'special_requests' => ['nullable', 'string', 'max:1000'],
            'estimated_arrival_time' => ['nullable', 'date_format:H:i'],
            'late_checkout_requested' => ['boolean'],
            'early_checkin_requested' => ['boolean'],
            'booking_source' => ['nullable', 'string', 'in:website,admin,agent,phone,email,walkin'],
            'internal_notes' => ['nullable', 'string', 'max:1000'],

            // Rooms array
            'rooms' => ['required', 'array', 'min:1'],
            'rooms.*.room_id' => ['required', 'integer', 'exists:hotel_rooms,id'],
            'rooms.*.rate_plan_id' => ['required', 'integer', 'exists:hotel_rate_plans,id'],
            'rooms.*.adults' => ['required', 'integer', 'min:1'],
            'rooms.*.children' => ['nullable', 'integer', 'min:0'],
            'rooms.*.infants' => ['nullable', 'integer', 'min:0'],
            'rooms.*.meal_plan' => ['nullable', 'string'],
            'rooms.*.special_requests' => ['nullable', 'string', 'max:500'],
            'rooms.*.smoking_room' => ['boolean'],
            'rooms.*.accessible_room' => ['boolean'],

            // Guests array (optional)
            'rooms.*.guests' => ['nullable', 'array'],
            'rooms.*.guests.*.title' => ['nullable', 'string', 'in:Mr,Mrs,Ms,Dr,Prof,Master,Miss'],
            'rooms.*.guests.*.first_name' => ['required', 'string', 'max:100'],
            'rooms.*.guests.*.last_name' => ['required', 'string', 'max:100'],
            'rooms.*.guests.*.guest_type' => ['nullable', 'string', 'in:adult,child,infant'],
            'rooms.*.guests.*.email' => ['nullable', 'email', 'max:255'],
            'rooms.*.guests.*.phone' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'hotel_id.required' => 'Please select a hotel.',
            'check_in_date.required' => 'Check-in date is required.',
            'check_out_date.required' => 'Check-out date is required.',
            'check_out_date.after' => 'Check-out date must be after check-in date.',
            'contact_name.required' => 'Contact name is required.',
            'contact_email.required' => 'Contact email is required.',
            'contact_phone.required' => 'Contact phone is required.',
            'rooms.required' => 'At least one room is required.',
            'rooms.*.room_id.required' => 'Please select a room.',
            'rooms.*.rate_plan_id.required' => 'Please select a rate plan.',
        ];
    }
}
