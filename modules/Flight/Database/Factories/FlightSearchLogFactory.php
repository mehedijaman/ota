<?php

namespace Modules\Flight\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Flight\Models\FlightAirport;
use Modules\Flight\Models\FlightSearchLog;

class FlightSearchLogFactory extends Factory
{
    protected $model = FlightSearchLog::class;

    public function definition(): array
    {
        return [
            'session_id' => $this->faker->uuid(),
            'trip_type' => $this->faker->randomElement(['one_way', 'round_trip']),
            'origin_airport_id' => FlightAirport::inRandomOrder()->first()?->id,
            'destination_airport_id' => FlightAirport::inRandomOrder()->first()?->id,
            'origin_code' => $this->faker->randomElement(['DAC', 'CGP', 'ZYL']),
            'destination_code' => $this->faker->randomElement(['DXB', 'SIN', 'KUL', 'BKK']),
            'departure_date' => $this->faker->dateTimeBetween('+1 week', '+3 months'),
            'return_date' => $this->faker->optional(0.5)->dateTimeBetween('+2 weeks', '+4 months'),
            'adults' => $this->faker->numberBetween(1, 4),
            'children' => $this->faker->numberBetween(0, 2),
            'infants' => $this->faker->numberBetween(0, 1),
            'cabin_class' => $this->faker->randomElement(['economy', 'business']),
            'direct_only' => $this->faker->boolean(30),
            'results_count' => $this->faker->numberBetween(5, 50),
            'lowest_price' => $this->faker->randomFloat(2, 8000, 20000),
            'highest_price' => $this->faker->randomFloat(2, 25000, 80000),
            'currency_code' => 'BDT',
            'response_time_ms' => $this->faker->numberBetween(500, 5000),
            'source' => $this->faker->randomElement(['website', 'mobile_app', 'agent_portal']),
            'converted_to_booking' => $this->faker->boolean(10),
            'ip_address' => $this->faker->ipv4(),
        ];
    }
}
