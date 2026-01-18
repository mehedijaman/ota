<?php

namespace Modules\Flight\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Flight\Models\Flight;
use Modules\Flight\Models\FlightAircraftType;
use Modules\Flight\Models\FlightAirline;
use Modules\Flight\Models\FlightAirport;
use Modules\Flight\Models\FlightSupplier;

class FlightFactory extends Factory
{
    protected $model = Flight::class;

    public function definition(): array
    {
        $departureTime = $this->faker->time('H:i');
        $durationMinutes = $this->faker->numberBetween(60, 600);
        $airline = FlightAirline::inRandomOrder()->first();

        return [
            'uuid' => $this->faker->uuid(),
            'supplier_id' => FlightSupplier::inRandomOrder()->first()?->id,
            'airline_id' => $airline?->id ?? FlightAirline::factory(),
            'operating_airline_id' => $this->faker->boolean(20) ? FlightAirline::inRandomOrder()->first()?->id : null,
            'flight_number' => $this->faker->numberBetween(100, 9999),
            'departure_airport_id' => FlightAirport::inRandomOrder()->first()?->id ?? FlightAirport::factory(),
            'arrival_airport_id' => FlightAirport::inRandomOrder()->first()?->id ?? FlightAirport::factory(),
            'aircraft_type_id' => FlightAircraftType::inRandomOrder()->first()?->id,
            'scheduled_departure_time' => $departureTime,
            'scheduled_arrival_time' => date('H:i', strtotime($departureTime) + $durationMinutes * 60),
            'duration_minutes' => $durationMinutes,
            'crosses_midnight' => $durationMinutes > 720,
            'day_offset' => $durationMinutes > 720 ? 1 : 0,
            'operates_monday' => $this->faker->boolean(80),
            'operates_tuesday' => $this->faker->boolean(80),
            'operates_wednesday' => $this->faker->boolean(80),
            'operates_thursday' => $this->faker->boolean(80),
            'operates_friday' => $this->faker->boolean(80),
            'operates_saturday' => $this->faker->boolean(70),
            'operates_sunday' => $this->faker->boolean(70),
            'effective_from' => now(),
            'effective_until' => now()->addMonths(6),
            'stops_count' => $this->faker->randomElement([0, 0, 0, 1]),
            'is_codeshare' => $this->faker->boolean(15),
            'has_wifi' => $this->faker->boolean(40),
            'has_power_outlet' => $this->faker->boolean(60),
            'has_entertainment' => $this->faker->boolean(50),
            'status' => 'scheduled',
            'is_active' => true,
            'base_price' => $this->faker->randomFloat(2, 5000, 50000),
        ];
    }

    public function direct(): static
    {
        return $this->state(fn (array $attributes) => [
            'stops_count' => 0,
        ]);
    }

    public function daily(): static
    {
        return $this->state(fn (array $attributes) => [
            'operates_monday' => true,
            'operates_tuesday' => true,
            'operates_wednesday' => true,
            'operates_thursday' => true,
            'operates_friday' => true,
            'operates_saturday' => true,
            'operates_sunday' => true,
        ]);
    }
}
