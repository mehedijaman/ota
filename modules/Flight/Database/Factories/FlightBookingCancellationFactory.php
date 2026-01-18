<?php

namespace Modules\Flight\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Flight\Models\FlightBooking;
use Modules\Flight\Models\FlightBookingCancellation;

class FlightBookingCancellationFactory extends Factory
{
    protected $model = FlightBookingCancellation::class;

    public function definition(): array
    {
        $bookingAmount = $this->faker->randomFloat(2, 10000, 100000);
        $penalty = round($bookingAmount * 0.25, 2);

        return [
            'uuid' => $this->faker->uuid(),
            'booking_id' => FlightBooking::factory(),
            'type' => 'full',
            'reason_category' => $this->faker->randomElement(['customer_request', 'schedule_change', 'medical']),
            'reason_details' => $this->faker->sentence(),
            'booking_amount' => $bookingAmount,
            'airline_penalty' => $penalty,
            'service_charge' => round($bookingAmount * 0.05, 2),
            'total_deduction' => $penalty + round($bookingAmount * 0.05, 2),
            'refundable_amount' => $bookingAmount - $penalty - round($bookingAmount * 0.05, 2),
            'refunded_amount' => 0,
            'currency_code' => 'BDT',
            'refund_status' => 'pending_approval',
            'status' => 'requested',
            'requested_at' => now(),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
            'refund_status' => 'approved',
            'confirmed_at' => now(),
        ]);
    }
}
