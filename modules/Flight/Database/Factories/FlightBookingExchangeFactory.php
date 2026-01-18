<?php

namespace Modules\Flight\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Flight\Models\FlightBooking;
use Modules\Flight\Models\FlightBookingExchange;

class FlightBookingExchangeFactory extends Factory
{
    protected $model = FlightBookingExchange::class;

    public function definition(): array
    {
        $originalFare = $this->faker->randomFloat(2, 20000, 80000);
        $newFare = $this->faker->randomFloat(2, 25000, 100000);

        return [
            'uuid' => $this->faker->uuid(),
            'booking_id' => FlightBooking::factory(),
            'type' => $this->faker->randomElement(['date_change', 'route_change', 'class_upgrade']),
            'reason' => $this->faker->sentence(),
            'change_details' => $this->faker->paragraph(),
            'original_departure_date' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'new_departure_date' => $this->faker->dateTimeBetween('+2 weeks', '+2 months'),
            'original_fare' => $originalFare,
            'new_fare' => $newFare,
            'fare_difference' => $newFare - $originalFare,
            'exchange_fee' => 3500,
            'service_fee' => 500,
            'total_due' => max(0, $newFare - $originalFare + 4000),
            'amount_collected' => 0,
            'currency_code' => 'BDT',
            'status' => 'requested',
            'requested_at' => now(),
        ];
    }
}
