<?php

namespace Modules\Visa\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Visa\Models\Visa;
use Modules\Visa\Models\VisaApplication;
use Modules\Visa\Models\VisaCountry;

class VisaApplicationFactory extends Factory
{
    protected $model = VisaApplication::class;

    public function definition(): array
    {
        $status = fake()->randomElement([
            'draft', 'pending', 'documents_pending', 'documents_submitted',
            'under_review', 'processing', 'submitted_to_embassy',
            'approved', 'completed',
        ]);

        $paymentStatus = fake()->randomElement(['unpaid', 'partial', 'paid']);

        $adultCount = fake()->numberBetween(1, 4);
        $childCount = fake()->numberBetween(0, 2);

        $subtotal = fake()->randomElement([15000, 20000, 25000, 30000, 40000]);
        $embassyFees = fake()->randomElement([5000, 8000, 10000, 15000]);
        $serviceFees = fake()->randomElement([3000, 5000, 7500]);
        $totalAmount = $subtotal + $embassyFees + $serviceFees;
        $amountPaid = $paymentStatus === 'paid' ? $totalAmount : ($paymentStatus === 'partial' ? $totalAmount * 0.5 : 0);

        return [
            'application_number' => 'VA-'.strtoupper(Str::random(8)),
            'visa_id' => Visa::factory(),
            'country_id' => VisaCountry::factory(),
            'processing_time_id' => null,
            'customer_id' => null,
            'agent_id' => null,
            'status' => $status,
            'payment_status' => $paymentStatus,
            'adult_count' => $adultCount,
            'child_count' => $childCount,
            'infant_count' => 0,
            'intended_travel_date' => fake()->dateTimeBetween('+1 month', '+6 months'),
            'intended_return_date' => fake()->dateTimeBetween('+2 months', '+7 months'),
            'travel_purpose' => fake()->randomElement(['Tourism', 'Business', 'Visit Family', 'Medical', 'Education']),
            'contact_name' => fake()->name(),
            'contact_email' => fake()->safeEmail(),
            'contact_phone' => fake()->phoneNumber(),
            'whatsapp' => fake()->phoneNumber(),
            'contact_address' => fake()->address(),
            'city' => fake()->city(),
            'postal_code' => fake()->postcode(),
            'country' => 'Bangladesh',
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_phone' => fake()->phoneNumber(),
            'emergency_contact_relationship' => fake()->randomElement(['Spouse', 'Parent', 'Sibling', 'Friend']),
            'subtotal' => $subtotal,
            'embassy_fees' => $embassyFees,
            'service_fees' => $serviceFees,
            'express_fees' => 0,
            'additional_fees' => 0,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => $totalAmount,
            'amount_paid' => $amountPaid,
            'amount_due' => $totalAmount - $amountPaid,
            'application_source' => fake()->randomElement(['website', 'admin', 'agent']),
            'special_requests' => fake()->optional()->sentence(),
            'submitted_at' => in_array($status, ['draft']) ? null : fake()->dateTimeBetween('-3 months', '-1 week'),
            'approved_at' => in_array($status, ['approved', 'completed']) ? fake()->dateTimeBetween('-1 month', 'now') : null,
            'completed_at' => $status === 'completed' ? fake()->dateTimeBetween('-2 weeks', 'now') : null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'processing',
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'payment_status' => 'paid',
            'approved_at' => now(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'payment_status' => 'paid',
            'approved_at' => now()->subWeek(),
            'completed_at' => now(),
        ]);
    }

    public function paid(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'payment_status' => 'paid',
                'amount_paid' => $attributes['total_amount'],
                'amount_due' => 0,
            ];
        });
    }
}
