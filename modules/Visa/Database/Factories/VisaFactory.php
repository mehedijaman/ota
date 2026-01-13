<?php

namespace Modules\Visa\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Visa\Models\Visa;
use Modules\Visa\Models\VisaCategory;
use Modules\Visa\Models\VisaCountry;

class VisaFactory extends Factory
{
    protected $model = Visa::class;

    public function definition(): array
    {
        $processingMin = fake()->randomElement([5, 7, 10, 14]);
        $processingMax = $processingMin + fake()->randomElement([3, 5, 7, 10]);

        $basePrice = fake()->randomElement([5000, 8000, 10000, 15000, 20000, 25000, 30000]);
        $serviceFee = fake()->randomElement([2000, 3000, 5000, 7500, 10000]);
        $embassyFee = fake()->randomElement([3000, 5000, 8000, 10000, 15000]);
        $vfsFee = fake()->randomElement([0, 1500, 2000, 2500, 3000]);

        $name = fake()->unique()->words(4, true).' Visa Package';

        return [
            'country_id' => VisaCountry::factory(),
            'category_id' => VisaCategory::factory(),
            'visa_type_id' => null,
            'name' => $name,
            'slug' => Str::slug($name),
            'code' => strtoupper(fake()->unique()->lexify('VISA-????-###')),
            'excerpt' => fake()->sentence(15),
            'description' => fake()->paragraphs(4, true),
            'eligibility_criteria' => fake()->paragraphs(2, true),
            'terms_conditions' => fake()->paragraphs(3, true),
            'important_notes' => fake()->paragraphs(2, true),
            'entry_type' => fake()->randomElement(['single', 'double', 'multiple']),
            'validity_days' => fake()->randomElement([30, 60, 90, 180, 365]),
            'stay_days' => fake()->randomElement([14, 30, 60, 90]),
            'processing_days_min' => $processingMin,
            'processing_days_max' => $processingMax,
            'base_price' => $basePrice,
            'service_fee' => $serviceFee,
            'embassy_fee' => $embassyFee,
            'vfs_fee' => $vfsFee,
            'success_rate' => fake()->randomFloat(2, 85, 99),
            'is_express_available' => fake()->boolean(60),
            'is_interview_required' => fake()->boolean(40),
            'is_biometric_required' => fake()->boolean(50),
            'is_appointment_required' => fake()->boolean(40),
            'is_travel_insurance_required' => fake()->boolean(70),
            'is_bank_statement_required' => true,
            'is_invitation_letter_required' => fake()->boolean(30),
            'is_hotel_booking_required' => fake()->boolean(60),
            'is_flight_booking_required' => fake()->boolean(50),
            'included_services' => [
                'Application form filling',
                'Document verification',
                'Embassy appointment booking',
                'Application submission',
                'Status tracking',
            ],
            'excluded_services' => [
                'Travel insurance',
                'Flight tickets',
                'Hotel reservations',
                'Translation services',
            ],
            'is_featured' => fake()->boolean(20),
            'is_popular' => fake()->boolean(30),
            'is_active' => true,
            'valid_from' => now()->subMonths(6),
            'valid_to' => now()->addYear(),
            'applications_count' => fake()->numberBetween(0, 500),
            'approved_count' => fake()->numberBetween(0, 400),
            'meta_title' => $name,
            'meta_description' => 'Apply for '.$name.' with our professional visa services.',
            'created_at' => fake()->dateTimeBetween('-1 year', '-6 month'),
            'updated_at' => fake()->dateTimeBetween('-5 month', 'now'),
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_popular' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function expressAvailable(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_express_available' => true,
        ]);
    }
}
