<?php

namespace Modules\Hotel\Http\Requests;

use Modules\Support\Http\Requests\Request;

class HotelValidate extends Request
{
    public function rules(): array
    {
        $hotelId = $this->route('id');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:hotels,slug,'.$hotelId],
            'code' => ['nullable', 'string', 'max:50', 'unique:hotels,code,'.$hotelId],
            'chain_id' => ['nullable', 'integer', 'exists:hotel_chains,id'],
            'country_id' => ['required', 'integer', 'exists:hotel_countries,id'],
            'city_id' => ['required', 'integer', 'exists:hotel_cities,id'],
            'area_id' => ['nullable', 'integer', 'exists:hotel_areas,id'],
            'star_rating' => ['required', 'integer', 'min:1', 'max:5'],
            'property_type' => ['required', 'string', 'in:hotel,resort,apartment,villa,boutique,hostel,guesthouse,motel,lodge,inn'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'address' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'check_in_time' => ['required', 'date_format:H:i'],
            'check_out_time' => ['required', 'date_format:H:i'],
            'early_check_in_time' => ['nullable', 'date_format:H:i'],
            'late_check_out_time' => ['nullable', 'date_format:H:i'],
            'total_rooms' => ['nullable', 'integer', 'min:0'],
            'total_floors' => ['nullable', 'integer', 'min:1'],
            'year_built' => ['nullable', 'integer', 'min:1800', 'max:'.date('Y')],
            'year_renovated' => ['nullable', 'integer', 'min:1800', 'max:'.date('Y')],
            'policies' => ['nullable', 'array'],
            'languages_spoken' => ['nullable', 'array'],
            'is_featured' => ['boolean'],
            'is_recommended' => ['boolean'],
            'is_active' => ['boolean'],
            'is_verified' => ['boolean'],
            'is_instant_bookable' => ['boolean'],
            'commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'markup_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['integer', 'exists:hotel_amenities,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Hotel name is required.',
            'country_id.required' => 'Please select a country.',
            'city_id.required' => 'Please select a city.',
            'star_rating.required' => 'Star rating is required.',
            'property_type.required' => 'Property type is required.',
            'address.required' => 'Hotel address is required.',
            'check_in_time.required' => 'Check-in time is required.',
            'check_out_time.required' => 'Check-out time is required.',
        ];
    }
}
