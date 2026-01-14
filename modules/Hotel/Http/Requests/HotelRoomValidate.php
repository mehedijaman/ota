<?php

namespace Modules\Hotel\Http\Requests;

use Modules\Support\Http\Requests\Request;

class HotelRoomValidate extends Request
{
    public function rules(): array
    {
        $roomId = $this->route('roomId');

        return [
            'hotel_id' => ['required', 'integer', 'exists:hotels,id'],
            'room_type_id' => ['required', 'integer', 'exists:hotel_room_types,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', 'unique:hotel_rooms,code,'.$roomId],
            'description' => ['nullable', 'string'],
            'bed_type' => ['required', 'string', 'in:single,double,queen,king,twin,bunk,sofa_bed'],
            'bed_count' => ['required', 'integer', 'min:1', 'max:10'],
            'bed_configuration' => ['nullable', 'array'],
            'max_adults' => ['required', 'integer', 'min:1', 'max:20'],
            'max_children' => ['nullable', 'integer', 'min:0', 'max:10'],
            'max_infants' => ['nullable', 'integer', 'min:0', 'max:5'],
            'max_occupancy' => ['required', 'integer', 'min:1', 'max:30'],
            'standard_occupancy' => ['required', 'integer', 'min:1', 'max:10'],
            'room_size_sqm' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'floor' => ['nullable', 'string', 'in:low,mid,high'],
            'view_type' => ['nullable', 'string', 'max:50'],
            'is_smoking' => ['boolean'],
            'is_accessible' => ['boolean'],
            'has_balcony' => ['boolean'],
            'has_terrace' => ['boolean'],
            'has_kitchen' => ['boolean'],
            'has_kitchenette' => ['boolean'],
            'total_inventory' => ['required', 'integer', 'min:1', 'max:1000'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['integer', 'exists:hotel_room_amenities,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'hotel_id.required' => 'Hotel is required.',
            'room_type_id.required' => 'Room type is required.',
            'name.required' => 'Room name is required.',
            'bed_type.required' => 'Bed type is required.',
            'max_adults.required' => 'Maximum adults is required.',
            'max_occupancy.required' => 'Maximum occupancy is required.',
            'total_inventory.required' => 'Total inventory is required.',
        ];
    }
}
