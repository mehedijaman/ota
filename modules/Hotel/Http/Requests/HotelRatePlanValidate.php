<?php

namespace Modules\Hotel\Http\Requests;

use Modules\Support\Http\Requests\Request;

class HotelRatePlanValidate extends Request
{
    public function rules(): array
    {
        $ratePlanId = $this->route('ratePlanId');

        return [
            'hotel_id' => ['required', 'integer', 'exists:hotels,id'],
            'cancellation_policy_id' => ['nullable', 'integer', 'exists:hotel_cancellation_policies,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', 'unique:hotel_rate_plans,code,'.$ratePlanId],
            'description' => ['nullable', 'string', 'max:1000'],
            'meal_plan' => ['required', 'string', 'in:room_only,breakfast,half_board,full_board,all_inclusive,breakfast_lunch,breakfast_dinner,self_catering'],
            'is_refundable' => ['boolean'],
            'is_pay_at_hotel' => ['boolean'],
            'requires_prepayment' => ['boolean'],
            'prepayment_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'advance_booking_min' => ['nullable', 'integer', 'min:0'],
            'advance_booking_max' => ['nullable', 'integer', 'min:0'],
            'min_nights' => ['nullable', 'integer', 'min:1'],
            'max_nights' => ['nullable', 'integer', 'min:1'],
            'min_rooms' => ['nullable', 'integer', 'min:1'],
            'max_rooms' => ['nullable', 'integer', 'min:1'],
            'valid_from' => ['nullable', 'date'],
            'valid_to' => ['nullable', 'date', 'after_or_equal:valid_from'],
            'applicable_days' => ['nullable', 'array'],
            'applicable_days.*' => ['integer', 'min:0', 'max:6'],
            'markup_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'is_default' => ['boolean'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'hotel_id.required' => 'Hotel is required.',
            'name.required' => 'Rate plan name is required.',
            'meal_plan.required' => 'Meal plan is required.',
        ];
    }
}
