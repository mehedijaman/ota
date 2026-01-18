<?php

namespace Modules\Flight\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Flight\Models\FlightBooking;
use Modules\Flight\Models\FlightBookingStatusHistory;

class FlightBookingStatusHistoryFactory extends Factory
{
    protected $model = FlightBookingStatusHistory::class;

    public function definition(): array
    {
        return [
            'booking_id' => FlightBooking::factory(),
            'old_status' => 'pending',
            'new_status' => $this->faker->randomElement(['confirmed', 'processing', 'ticketed']),
            'status_type' => 'booking',
            'notes' => $this->faker->optional()->sentence(),
            'source' => $this->faker->randomElement(['user', 'system', 'api']),
            'ip_address' => $this->faker->ipv4(),
        ];
    }
}
