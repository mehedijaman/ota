<?php

namespace Modules\Flight\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Flight\Models\FlightAirport;
use Modules\Flight\Models\FlightPopularRoute;

class FlightPopularRouteFactory extends Factory
{
    protected $model = FlightPopularRoute::class;

    public function definition(): array
    {
        $origin = FlightAirport::inRandomOrder()->first();
        $destination = FlightAirport::inRandomOrder()->where('id', '!=', $origin?->id)->first();

        return [
            'origin_airport_id' => $origin?->id ?? FlightAirport::factory(),
            'destination_airport_id' => $destination?->id ?? FlightAirport::factory(),
            'origin_code' => $origin?->iata_code ?? 'DAC',
            'destination_code' => $destination?->iata_code ?? 'DXB',
            'route_name' => ($origin?->city ?? 'Dhaka').' to '.($destination?->city ?? 'Dubai'),
            'search_count' => $this->faker->numberBetween(1000, 50000),
            'booking_count' => $this->faker->numberBetween(100, 5000),
            'conversion_rate' => $this->faker->randomFloat(2, 5, 15),
            'avg_price' => $this->faker->randomFloat(2, 15000, 40000),
            'min_price' => $this->faker->randomFloat(2, 10000, 25000),
            'max_price' => $this->faker->randomFloat(2, 40000, 100000),
            'currency_code' => 'BDT',
            'rank' => $this->faker->unique()->numberBetween(1, 50),
            'is_featured' => $this->faker->boolean(20),
            'is_active' => true,
            'avg_duration_minutes' => $this->faker->numberBetween(120, 480),
            'last_calculated_at' => now(),
        ];
    }
}
