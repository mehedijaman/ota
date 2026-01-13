<?php

namespace Modules\Visa\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Support\Http\Requests\Request;

class VisaValidate extends Request
{
    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'country_id' => ['required', 'exists:visa_countries,id'],
            'category_id' => ['required', 'exists:visa_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('visas', 'slug')->ignore($id)],
            'code' => ['nullable', 'string', 'max:50', Rule::unique('visas', 'code')->ignore($id)],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'eligibility_criteria' => ['nullable', 'string'],
            'terms_conditions' => ['nullable', 'string'],
            'important_notes' => ['nullable', 'string'],
            'entry_type' => ['required', Rule::in(['single', 'double', 'multiple'])],
            'validity_days' => ['required', 'integer', 'min:1'],
            'stay_days' => ['required', 'integer', 'min:1'],
            'processing_days_min' => ['required', 'integer', 'min:1'],
            'processing_days_max' => ['required', 'integer', 'min:1', 'gte:processing_days_min'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'service_fee' => ['nullable', 'numeric', 'min:0'],
            'embassy_fee' => ['nullable', 'numeric', 'min:0'],
            'vfs_fee' => ['nullable', 'numeric', 'min:0'],
            'success_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'is_express_available' => ['boolean'],
            'is_interview_required' => ['boolean'],
            'is_biometric_required' => ['boolean'],
            'is_appointment_required' => ['boolean'],
            'is_travel_insurance_required' => ['boolean'],
            'is_bank_statement_required' => ['boolean'],
            'is_invitation_letter_required' => ['boolean'],
            'is_hotel_booking_required' => ['boolean'],
            'is_flight_booking_required' => ['boolean'],
            'included_services' => ['nullable', 'array'],
            'included_services.*' => ['string', 'max:255'],
            'excluded_services' => ['nullable', 'array'],
            'excluded_services.*' => ['string', 'max:255'],
            'is_featured' => ['boolean'],
            'is_popular' => ['boolean'],
            'is_active' => ['boolean'],
            'valid_from' => ['nullable', 'date'],
            'valid_to' => ['nullable', 'date', 'after_or_equal:valid_from'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'banner' => ['nullable', 'image', 'max:5120'], // 5MB max
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['image', 'max:2048'],
            'remove_thumbnail' => ['nullable', 'boolean'],
            'remove_banner' => ['nullable', 'boolean'],
            'remove_gallery_images' => ['nullable', 'array'],
            'remove_gallery_images.*' => ['integer'],

            // Requirements
            'requirements' => ['nullable', 'array'],
            'requirements.*.name' => ['required', 'string', 'max:255'],
            'requirements.*.slug' => ['required', 'string', 'max:255'],
            'requirements.*.type' => ['required', Rule::in(['document', 'form', 'photo', 'biometric', 'financial', 'travel', 'personal', 'information', 'other'])],
            'requirements.*.description' => ['nullable', 'string'],
            'requirements.*.instructions' => ['nullable', 'string'],
            'requirements.*.is_mandatory' => ['boolean'],
            'requirements.*.needs_upload' => ['boolean'],
            'requirements.*.accepted_formats' => ['nullable', 'array'],
            'requirements.*.max_file_size_kb' => ['nullable', 'integer', 'min:1'],

            // Fees
            'fees' => ['nullable', 'array'],
            'fees.*.name' => ['required', 'string', 'max:255'],
            'fees.*.fee_type' => ['required', Rule::in(['embassy', 'service', 'vfs', 'courier', 'insurance', 'translation', 'express', 'other'])],
            'fees.*.amount' => ['required', 'numeric', 'min:0'],
            'fees.*.description' => ['nullable', 'string'],
            'fees.*.is_per_person' => ['boolean'],
            'fees.*.is_refundable' => ['boolean'],
            'fees.*.is_optional' => ['boolean'],

            // Processing Times
            'processing_times' => ['nullable', 'array'],
            'processing_times.*.name' => ['required', 'string', 'max:255'],
            'processing_times.*.type' => ['required', Rule::in(['normal', 'express', 'urgent', 'super_urgent'])],
            'processing_times.*.processing_days_min' => ['required', 'integer', 'min:1'],
            'processing_times.*.processing_days_max' => ['required', 'integer', 'min:1'],
            'processing_times.*.additional_fee' => ['nullable', 'numeric', 'min:0'],
            'processing_times.*.description' => ['nullable', 'string'],

            // FAQs
            'faqs' => ['nullable', 'array'],
            'faqs.*.question' => ['required', 'string', 'max:500'],
            'faqs.*.answer' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'country_id.required' => 'Please select a country.',
            'category_id.required' => 'Please select a visa category.',
            'name.required' => 'Visa name is required.',
            'slug.required' => 'URL slug is required.',
            'slug.unique' => 'This slug is already taken.',
            'entry_type.required' => 'Entry type is required.',
            'validity_days.required' => 'Validity days is required.',
            'stay_days.required' => 'Stay days is required.',
            'processing_days_min.required' => 'Minimum processing days is required.',
            'processing_days_max.required' => 'Maximum processing days is required.',
            'processing_days_max.gte' => 'Maximum processing days must be greater than or equal to minimum.',
            'base_price.required' => 'Base price is required.',
        ];
    }
}
