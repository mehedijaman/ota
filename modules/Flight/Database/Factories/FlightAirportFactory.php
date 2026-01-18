<?php

namespace Modules\Flight\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Flight\Models\FlightAirport;
use Nnjeim\World\Models\Country;

class FlightAirportFactory extends Factory
{
    protected $model = FlightAirport::class;

    protected array $airports = [
        ['iata' => 'DAC', 'name' => 'Hazrat Shahjalal International Airport', 'city' => 'Dhaka', 'country' => 'BD', 'lat' => 23.8433, 'lng' => 90.3978, 'tz' => 'Asia/Dhaka'],
        ['iata' => 'CGP', 'name' => 'Shah Amanat International Airport', 'city' => 'Chittagong', 'country' => 'BD', 'lat' => 22.2496, 'lng' => 91.8133, 'tz' => 'Asia/Dhaka'],
        ['iata' => 'DXB', 'name' => 'Dubai International Airport', 'city' => 'Dubai', 'country' => 'AE', 'lat' => 25.2528, 'lng' => 55.3644, 'tz' => 'Asia/Dubai'],
        ['iata' => 'SIN', 'name' => 'Singapore Changi Airport', 'city' => 'Singapore', 'country' => 'SG', 'lat' => 1.3644, 'lng' => 103.9915, 'tz' => 'Asia/Singapore'],
        ['iata' => 'KUL', 'name' => 'Kuala Lumpur International Airport', 'city' => 'Kuala Lumpur', 'country' => 'MY', 'lat' => 2.7456, 'lng' => 101.7072, 'tz' => 'Asia/Kuala_Lumpur'],
        ['iata' => 'BKK', 'name' => 'Suvarnabhumi Airport', 'city' => 'Bangkok', 'country' => 'TH', 'lat' => 13.6900, 'lng' => 100.7501, 'tz' => 'Asia/Bangkok'],
        ['iata' => 'LHR', 'name' => 'Heathrow Airport', 'city' => 'London', 'country' => 'GB', 'lat' => 51.4700, 'lng' => -0.4543, 'tz' => 'Europe/London'],
        ['iata' => 'JFK', 'name' => 'John F Kennedy International Airport', 'city' => 'New York', 'country' => 'US', 'lat' => 40.6413, 'lng' => -73.7781, 'tz' => 'America/New_York'],
        ['iata' => 'DEL', 'name' => 'Indira Gandhi International Airport', 'city' => 'New Delhi', 'country' => 'IN', 'lat' => 28.5562, 'lng' => 77.1000, 'tz' => 'Asia/Kolkata'],
        ['iata' => 'DOH', 'name' => 'Hamad International Airport', 'city' => 'Doha', 'country' => 'QA', 'lat' => 25.2731, 'lng' => 51.6081, 'tz' => 'Asia/Qatar'],
    ];

    public function definition(): array
    {
        $airport = fake()->randomElement($this->airports);
        $country = Country::where('iso2', $airport['country'])->first();

        return [
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
            'country_id' => $country?->id ?? Country::inRandomOrder()->first()?->id ?? Country::create(['name' => 'Default', 'iso2' => 'DF', 'iso3' => 'DFT', 'phone_code' => '000', 'region' => 'Asia', 'subregion' => 'Southern Asia'])->id,
            'name' => $airport['name'],
            'iata_code' => $airport['iata'].$this->faker->unique()->numberBetween(1, 99),
            'icao_code' => 'K'.$airport['iata'],
            'city' => $airport['city'],
            'latitude' => $airport['lat'],
            'longitude' => $airport['lng'],
            'timezone' => $airport['tz'],
            'utc_offset_minutes' => 360,
            'type' => $this->faker->randomElement(['large_airport', 'medium_airport']),
            'is_international' => true,
            'is_domestic' => true,
            'terminals_count' => $this->faker->numberBetween(1, 4),
            'is_active' => true,
            'is_hub' => $this->faker->boolean(30),
            'is_popular' => $this->faker->boolean(50),
            'search_priority' => $this->faker->numberBetween(0, 100),
        ];
    }

    public function international(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_international' => true,
            'type' => 'large_airport',
        ]);
    }

    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_popular' => true,
            'search_priority' => $this->faker->numberBetween(80, 100),
        ]);
    }
}
