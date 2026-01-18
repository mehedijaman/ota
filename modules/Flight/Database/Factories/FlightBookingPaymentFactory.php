<?php

namespace Modules\Flight\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Flight\Models\FlightBooking;
use Modules\Flight\Models\FlightBookingPayment;

class FlightBookingPaymentFactory extends Factory
{
    protected $model = FlightBookingPayment::class;

    public function definition(): array
    {
        $amount = $this->faker->randomFloat(2, 5000, 50000);

        return [
            'uuid' => $this->faker->uuid(),
            'booking_id' => FlightBooking::factory(),
            'type' => 'payment',
            'method' => $this->faker->randomElement(['cash', 'bank_transfer', 'credit_card', 'bkash', 'nagad']),
            'amount' => $amount,
            'currency_code' => 'BDT',
            'exchange_rate' => 1.0,
            'amount_in_base_currency' => $amount,
            'processing_fee' => 0,
            'gateway_fee' => 0,
            'net_amount' => $amount,
            'status' => 'completed',
            'reference_number' => 'PAY-'.strtoupper($this->faker->bothify('??####')),
            'is_verified' => true,
            'verified_at' => now(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'is_verified' => false,
            'verified_at' => null,
        ]);
    }

    public function refund(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'refund',
        ]);
    }
}
