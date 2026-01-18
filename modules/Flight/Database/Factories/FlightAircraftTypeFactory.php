<?php

namespace Modules\Flight\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Flight\Models\FlightAircraftType;

class FlightAircraftTypeFactory extends Factory
{
    protected $model = FlightAircraftType::class;

    protected array $aircraft = [
        ['name' => 'Boeing 737-800', 'manufacturer' => 'Boeing', 'model' => '737', 'variant' => '800', 'category' => 'narrow_body', 'seats' => 189],
        ['name' => 'Boeing 777-300ER', 'manufacturer' => 'Boeing', 'model' => '777', 'variant' => '300ER', 'category' => 'wide_body', 'seats' => 396],
        ['name' => 'Boeing 787-9', 'manufacturer' => 'Boeing', 'model' => '787', 'variant' => '9', 'category' => 'wide_body', 'seats' => 290],
        ['name' => 'Airbus A320neo', 'manufacturer' => 'Airbus', 'model' => 'A320', 'variant' => 'neo', 'category' => 'narrow_body', 'seats' => 180],
        ['name' => 'Airbus A350-900', 'manufacturer' => 'Airbus', 'model' => 'A350', 'variant' => '900', 'category' => 'wide_body', 'seats' => 325],
        ['name' => 'Airbus A380-800', 'manufacturer' => 'Airbus', 'model' => 'A380', 'variant' => '800', 'category' => 'wide_body', 'seats' => 525],
        ['name' => 'Bombardier Dash 8 Q400', 'manufacturer' => 'Bombardier', 'model' => 'Dash 8', 'variant' => 'Q400', 'category' => 'regional_jet', 'seats' => 78],
        ['name' => 'Embraer E195', 'manufacturer' => 'Embraer', 'model' => 'E195', 'variant' => 'E2', 'category' => 'regional_jet', 'seats' => 132],
    ];

    public function definition(): array
    {
        $aircraft = $this->faker->randomElement($this->aircraft);

        return [
            'uuid' => $this->faker->uuid(),
            'name' => $aircraft['name'],
            'iata_code' => strtoupper(substr($aircraft['model'], 0, 3)),
            'icao_code' => 'B'.$this->faker->randomNumber(3),
            'manufacturer' => $aircraft['manufacturer'],
            'model' => $aircraft['model'],
            'variant' => $aircraft['variant'],
            'category' => $aircraft['category'],
            'engine_type' => 'jet',
            'engine_count' => $aircraft['category'] === 'wide_body' ? 2 : 2,
            'typical_seats' => $aircraft['seats'],
            'max_seats' => (int) ($aircraft['seats'] * 1.1),
            'economy_class_seats' => (int) ($aircraft['seats'] * 0.85),
            'business_class_seats' => (int) ($aircraft['seats'] * 0.1),
            'first_class_seats' => (int) ($aircraft['seats'] * 0.05),
            'range_km' => $this->faker->numberBetween(4000, 15000),
            'cruise_speed_kmh' => $this->faker->numberBetween(850, 950),
            'is_active' => true,
        ];
    }

    public function wideBody(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'wide_body',
        ]);
    }

    public function narrowBody(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'narrow_body',
        ]);
    }
}
