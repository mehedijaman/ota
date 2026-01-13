<?php

namespace Modules\Visa\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Visa\Models\Visa;
use Modules\Visa\Models\VisaCategory;
use Modules\Visa\Models\VisaCountry;
use Modules\Visa\Models\VisaFaq;
use Modules\Visa\Models\VisaFee;
use Modules\Visa\Models\VisaProcessingTime;
use Modules\Visa\Models\VisaRequirement;

class VisaSeeder extends Seeder
{
    public function run(): void
    {
        $countries = VisaCountry::all();
        $touristCategory = VisaCategory::where('code', 'TOURIST')->first();
        $businessCategory = VisaCategory::where('code', 'BUSINESS')->first();

        if ($countries->isEmpty() || ! $touristCategory) {
            $this->command->warn('Please run VisaCountrySeeder and VisaCategorySeeder first.');

            return;
        }

        foreach ($countries as $country) {
            // Create Tourist Visa for each country
            $touristVisa = $this->createVisa($country, $touristCategory, 'Tourist Visa', [
                'base_price' => rand(10, 25) * 1000,
                'service_fee' => rand(3, 8) * 1000,
                'embassy_fee' => rand(5, 15) * 1000,
                'validity_days' => 90,
                'stay_days' => 30,
                'is_popular' => true,
            ]);

            $this->createVisaRelatedData($touristVisa);

            // Create Business Visa for half the countries
            if ($businessCategory && rand(0, 1)) {
                $businessVisa = $this->createVisa($country, $businessCategory, 'Business Visa', [
                    'base_price' => rand(20, 40) * 1000,
                    'service_fee' => rand(5, 10) * 1000,
                    'embassy_fee' => rand(10, 25) * 1000,
                    'validity_days' => 180,
                    'stay_days' => 90,
                    'is_interview_required' => true,
                ]);

                $this->createVisaRelatedData($businessVisa);
            }
        }
    }

    protected function createVisa(VisaCountry $country, VisaCategory $category, string $type, array $overrides = []): Visa
    {
        $name = $country->name.' '.$type.' Package';

        $defaults = [
            'country_id' => $country->id,
            'category_id' => $category->id,
            'name' => $name,
            'slug' => Str::slug($name),
            'code' => strtoupper($country->code.'-'.Str::slug($type).'-'.rand(100, 999)),
            'excerpt' => 'Apply for '.$country->name.' '.$type.' with our professional visa services.',
            'description' => 'Our '.$country->name.' '.$type.' service provides comprehensive assistance from application to approval. We handle all documentation, embassy appointments, and follow-ups.',
            'eligibility_criteria' => "To apply for this visa, applicants must:\n- Hold a valid passport with at least 6 months validity\n- Have sufficient financial resources\n- Provide purpose of travel documentation\n- Have no criminal record",
            'terms_conditions' => "By applying through our service, you agree to:\n- Provide accurate and truthful information\n- Submit all required documents on time\n- Pay all applicable fees\n- Attend interviews if required",
            'important_notes' => "Please note:\n- Processing times may vary based on embassy workload\n- Embassy fees are non-refundable\n- Additional documents may be requested",
            'entry_type' => 'single',
            'validity_days' => 90,
            'stay_days' => 30,
            'processing_days_min' => 7,
            'processing_days_max' => 15,
            'base_price' => 15000,
            'service_fee' => 5000,
            'embassy_fee' => 10000,
            'vfs_fee' => 2000,
            'success_rate' => rand(85, 98) + (rand(0, 99) / 100),
            'is_express_available' => true,
            'is_interview_required' => false,
            'is_biometric_required' => rand(0, 1),
            'is_appointment_required' => rand(0, 1),
            'is_travel_insurance_required' => true,
            'is_bank_statement_required' => true,
            'is_invitation_letter_required' => false,
            'is_hotel_booking_required' => true,
            'is_flight_booking_required' => true,
            'included_services' => [
                'Application form filling assistance',
                'Document verification and checklist',
                'Embassy appointment booking',
                'Application submission',
                'Real-time status tracking',
                'Customer support',
            ],
            'excluded_services' => [
                'Travel insurance premium',
                'Flight ticket booking',
                'Hotel reservation costs',
                'Document translation fees',
                'Notarization charges',
            ],
            'is_featured' => rand(0, 1),
            'is_popular' => false,
            'is_active' => true,
            'valid_from' => now()->subMonths(3),
            'valid_to' => now()->addYear(),
            'meta_title' => $name.' | Apply Online',
            'meta_description' => 'Apply for '.$country->name.' '.$type.' online. Fast processing, expert guidance.',
        ];

        return Visa::firstOrCreate(
            ['slug' => Str::slug($name)],
            array_merge($defaults, $overrides)
        );
    }

    protected function createVisaRelatedData(Visa $visa): void
    {
        // Create Requirements
        $requirements = [
            ['name' => 'Valid Passport', 'type' => 'document', 'mandatory' => true, 'instructions' => 'Passport must be valid for at least 6 months beyond your planned stay.'],
            ['name' => 'Passport Size Photos', 'type' => 'document', 'mandatory' => true, 'instructions' => '2 recent photos, white background, 35mm x 45mm.'],
            ['name' => 'Bank Statement', 'type' => 'financial', 'mandatory' => true, 'instructions' => 'Last 6 months bank statement showing sufficient funds.'],
            ['name' => 'National ID Card', 'type' => 'document', 'mandatory' => true, 'instructions' => 'Copy of your National ID card (both sides).'],
            ['name' => 'Travel Itinerary', 'type' => 'information', 'mandatory' => true, 'instructions' => 'Day-by-day travel plan including places to visit.'],
            ['name' => 'Hotel Booking', 'type' => 'document', 'mandatory' => false, 'instructions' => 'Confirmed hotel reservation for your stay.'],
            ['name' => 'Flight Reservation', 'type' => 'document', 'mandatory' => false, 'instructions' => 'Return flight ticket or reservation.'],
            ['name' => 'Travel Insurance', 'type' => 'document', 'mandatory' => false, 'instructions' => 'Travel insurance covering medical expenses.'],
        ];

        foreach ($requirements as $index => $req) {
            VisaRequirement::firstOrCreate(
                ['visa_id' => $visa->id, 'slug' => Str::slug($req['name'])],
                [
                    'name' => $req['name'],
                    'type' => $req['type'],
                    'description' => $req['instructions'],
                    'instructions' => $req['instructions'],
                    'is_mandatory' => $req['mandatory'],
                    'needs_upload' => true,
                    'accepted_formats' => ['pdf', 'jpg', 'jpeg', 'png'],
                    'max_file_size_kb' => 5120,
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }

        // Create Fees
        $fees = [
            ['name' => 'Embassy Fee', 'type' => 'embassy', 'amount' => $visa->embassy_fee, 'refundable' => false],
            ['name' => 'Service Fee', 'type' => 'service', 'amount' => $visa->service_fee, 'refundable' => true],
            ['name' => 'VFS Fee', 'type' => 'vfs', 'amount' => $visa->vfs_fee, 'refundable' => false],
            ['name' => 'Express Processing', 'type' => 'express', 'amount' => 5000, 'refundable' => false, 'optional' => true],
        ];

        foreach ($fees as $index => $fee) {
            VisaFee::firstOrCreate(
                ['visa_id' => $visa->id, 'name' => $fee['name']],
                [
                    'description' => ucfirst($fee['type']).' charges for visa processing.',
                    'fee_type' => $fee['type'],
                    'amount' => $fee['amount'],
                    'is_per_person' => true,
                    'is_refundable' => $fee['refundable'],
                    'is_optional' => $fee['optional'] ?? false,
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }

        // Create Processing Times
        $processingTimes = [
            ['name' => 'Normal Processing', 'type' => 'normal', 'min' => 10, 'max' => 15, 'fee' => 0],
            ['name' => 'Express Processing', 'type' => 'express', 'min' => 5, 'max' => 7, 'fee' => 5000],
            ['name' => 'Urgent Processing', 'type' => 'urgent', 'min' => 3, 'max' => 5, 'fee' => 10000],
        ];

        foreach ($processingTimes as $index => $pt) {
            VisaProcessingTime::firstOrCreate(
                ['visa_id' => $visa->id, 'type' => $pt['type']],
                [
                    'name' => $pt['name'],
                    'description' => $pt['name'].' - '.$pt['min'].' to '.$pt['max'].' working days.',
                    'processing_days_min' => $pt['min'],
                    'processing_days_max' => $pt['max'],
                    'additional_fee' => $pt['fee'],
                    'is_guaranteed' => $pt['type'] !== 'normal',
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }

        // Create FAQs
        $faqs = [
            ['q' => 'What is the processing time?', 'a' => 'Standard processing takes 10-15 working days. Express and urgent options are available.'],
            ['q' => 'What documents are required?', 'a' => 'You need a valid passport, photos, bank statements, and supporting documents. Check our requirements list.'],
            ['q' => 'Can I track my application?', 'a' => 'Yes, you can track your application status through our online portal.'],
            ['q' => 'What if my application is rejected?', 'a' => 'Our service fee is partially refundable. Embassy fees are non-refundable.'],
        ];

        foreach ($faqs as $index => $faq) {
            VisaFaq::firstOrCreate(
                ['visa_id' => $visa->id, 'question' => $faq['q']],
                [
                    'answer' => $faq['a'],
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }
    }
}
