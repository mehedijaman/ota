<?php

namespace Modules\Tour\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Support\Http\Requests\Request;

class TourValidate extends Request
{
    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'category_id' => ['nullable', 'exists:tour_categories,id'],
            'destination_id' => ['nullable', 'exists:tour_destinations,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('tours', 'slug')->ignore($id)],
            'code' => ['nullable', 'string', 'max:20', Rule::unique('tours', 'code')->ignore($id)],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'duration_nights' => ['required', 'integer', 'min:0'],
            'min_group_size' => ['nullable', 'integer', 'min:1'],
            'max_group_size' => ['nullable', 'integer', 'min:1'],
            'difficulty_level' => ['required', Rule::in(['easy', 'moderate', 'challenging', 'extreme'])],
            'tour_type' => ['required', Rule::in(['group', 'private', 'custom'])],
            'base_price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0'],
            'child_price' => ['nullable', 'numeric', 'min:0'],
            'infant_price' => ['nullable', 'numeric', 'min:0'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'deposit_type' => ['nullable', Rule::in(['fixed', 'percentage'])],
            'cancellation_days' => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['boolean'],
            'is_bestseller' => ['boolean'],
            'is_active' => ['boolean'],
            'start_location' => ['nullable', 'string', 'max:255'],
            'end_location' => ['nullable', 'string', 'max:255'],
            'languages' => ['nullable', 'array'],
            'age_restrictions' => ['nullable', 'array'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
            'valid_from' => ['nullable', 'date'],
            'valid_to' => ['nullable', 'date', 'after_or_equal:valid_from'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['image', 'max:2048'],
            'remove_previous_thumbnail' => ['nullable', 'boolean'],

            // Itineraries
            'itineraries' => ['nullable', 'array'],
            'itineraries.*.day_number' => ['required', 'integer', 'min:1'],
            'itineraries.*.title' => ['required', 'string', 'max:255'],
            'itineraries.*.description' => ['nullable', 'string'],
            'itineraries.*.meals_included' => ['nullable', 'array'],
            'itineraries.*.accommodation' => ['nullable', 'string'],

            // Inclusions
            'inclusions' => ['nullable', 'array'],
            'inclusions.*' => ['string', 'max:255'],

            // Exclusions
            'exclusions' => ['nullable', 'array'],
            'exclusions.*' => ['string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tour name is required.',
            'slug.required' => 'URL slug is required.',
            'slug.unique' => 'This slug is already taken.',
            'duration_days.required' => 'Duration (days) is required.',
            'base_price.required' => 'Base price is required.',
        ];
    }
}
