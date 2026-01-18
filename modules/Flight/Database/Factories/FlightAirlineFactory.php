<?php

namespace Modules\Flight\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Flight\Models\FlightAirline;
use Modules\Flight\Models\FlightAirport;
use Nnjeim\World\Models\Country;

class FlightAirlineFactory extends Factory
{
    protected $model = FlightAirline::class;

    protected array $airlines = [
        ['name' => 'Biman Bangladesh Airlines', 'iata' => 'BG', 'icao' => 'BBC', 'country' => 'BD', 'type' => 'scheduled', 'alliance' => 'none'],
        ['name' => 'US-Bangla Airlines', 'iata' => 'BS', 'icao' => 'UBG', 'country' => 'BD', 'type' => 'scheduled', 'alliance' => 'none'],
        ['name' => 'Novoair', 'iata' => 'VQ', 'icao' => 'NVQ', 'country' => 'BD', 'type' => 'scheduled', 'alliance' => 'none'],
        ['name' => 'Emirates', 'iata' => 'EK', 'icao' => 'UAE', 'country' => 'AE', 'type' => 'scheduled', 'alliance' => 'none'],
        ['name' => 'Qatar Airways', 'iata' => 'QR', 'icao' => 'QTR', 'country' => 'QA', 'type' => 'scheduled', 'alliance' => 'oneworld'],
        ['name' => 'Singapore Airlines', 'iata' => 'SQ', 'icao' => 'SIA', 'country' => 'SG', 'type' => 'scheduled', 'alliance' => 'star_alliance'],
        ['name' => 'Thai Airways', 'iata' => 'TG', 'icao' => 'THA', 'country' => 'TH', 'type' => 'scheduled', 'alliance' => 'star_alliance'],
        ['name' => 'Malaysia Airlines', 'iata' => 'MH', 'icao' => 'MAS', 'country' => 'MY', 'type' => 'scheduled', 'alliance' => 'oneworld'],
        ['name' => 'IndiGo', 'iata' => '6E', 'icao' => 'IGO', 'country' => 'IN', 'type' => 'low_cost', 'alliance' => 'none'],
        ['name' => 'Air Arabia', 'iata' => 'G9', 'icao' => 'ABY', 'country' => 'AE', 'type' => 'low_cost', 'alliance' => 'none'],
    ];

    public function definition(): array
    {
        $airline = $this->faker->randomElement($this->airlines);
        $country = Country::where('iso2', $airline['country'])->first();

        return [
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
            'country_id' => $country?->id ?? Country::inRandomOrder()->first()?->id ?? Country::create(['name' => 'Default', 'iso2' => 'DF', 'iso3' => 'DFT', 'phone_code' => '000', 'region' => 'Asia', 'subregion' => 'Southern Asia'])->id,
            'name' => $airline['name'],
            'iata_code' => $airline['iata'].$this->faker->unique()->numberBetween(1, 9),
            'icao_code' => $airline['icao'],
            'call_sign' => strtoupper($this->faker->word()),
            'type' => $airline['type'],
            'alliance' => $airline['alliance'],
            'hub_airport_id' => FlightAirport::inRandomOrder()->first()?->id,
            'founded_year' => $this->faker->year('-50 years'),
            'fleet_size' => $this->faker->numberBetween(20, 300),
            'destinations_count' => $this->faker->numberBetween(30, 200),
            'headquarters' => $this->faker->city(),
            'website' => 'https://www.'.strtolower(preg_replace('/[^a-zA-Z]/', '', $airline['name'])).'.com',
            'customer_service_phone' => $this->faker->phoneNumber(),
            'customer_service_email' => 'support@'.strtolower(preg_replace('/[^a-zA-Z]/', '', $airline['name'])).'.com',
            'baggage_policy' => $this->generateBaggagePolicy($airline['type']),
            'allows_web_checkin' => true,
            'checkin_hours_before' => 24,
            'default_commission_rate' => $this->faker->randomFloat(2, 5, 12),
            'default_markup_rate' => $this->faker->randomFloat(2, 3, 8),
            'is_active' => true,
            'is_featured' => $this->faker->boolean(30),
            'operating_status' => 'active',
            'avg_rating' => $this->faker->randomFloat(2, 3.5, 5),
            'reviews_count' => $this->faker->numberBetween(0, 1000),
        ];
    }

    protected function generateBaggagePolicy(string $type): array
    {
        if ($type === 'low_cost') {
            return [
                'cabin' => ['weight' => 7, 'pieces' => 1],
                'checked' => ['weight' => 0, 'pieces' => 0, 'fee' => 'Paid'],
            ];
        }

        return [
            'cabin' => ['weight' => 7, 'pieces' => 1],
            'checked' => ['weight' => 23, 'pieces' => 1, 'fee' => 'Included'],
        ];
    }

    public function lowCost(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'low_cost',
            'baggage_policy' => $this->generateBaggagePolicy('low_cost'),
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
}
