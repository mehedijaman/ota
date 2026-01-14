<?php

namespace Modules\Hotel\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Hotel\Models\HotelCountry;

class HotelCountryFactory extends Factory
{
    protected $model = HotelCountry::class;

    protected array $countries = [
        ['name' => 'Bangladesh', 'code' => 'BGD', 'code_alpha2' => 'BD', 'currency_code' => 'BDT', 'phone_code' => '+880'],
        ['name' => 'India', 'code' => 'IND', 'code_alpha2' => 'IN', 'currency_code' => 'INR', 'phone_code' => '+91'],
        ['name' => 'Thailand', 'code' => 'THA', 'code_alpha2' => 'TH', 'currency_code' => 'THB', 'phone_code' => '+66'],
        ['name' => 'Malaysia', 'code' => 'MYS', 'code_alpha2' => 'MY', 'currency_code' => 'MYR', 'phone_code' => '+60'],
        ['name' => 'Singapore', 'code' => 'SGP', 'code_alpha2' => 'SG', 'currency_code' => 'SGD', 'phone_code' => '+65'],
        ['name' => 'United Arab Emirates', 'code' => 'ARE', 'code_alpha2' => 'AE', 'currency_code' => 'AED', 'phone_code' => '+971'],
        ['name' => 'Saudi Arabia', 'code' => 'SAU', 'code_alpha2' => 'SA', 'currency_code' => 'SAR', 'phone_code' => '+966'],
        ['name' => 'United Kingdom', 'code' => 'GBR', 'code_alpha2' => 'GB', 'currency_code' => 'GBP', 'phone_code' => '+44'],
        ['name' => 'United States', 'code' => 'USA', 'code_alpha2' => 'US', 'currency_code' => 'USD', 'phone_code' => '+1'],
        ['name' => 'Nepal', 'code' => 'NPL', 'code_alpha2' => 'NP', 'currency_code' => 'NPR', 'phone_code' => '+977'],
    ];

    public function definition(): array
    {
        $country = $this->faker->unique()->randomElement($this->countries);

        return [
            'name' => $country['name'],
            'code' => $country['code'],
            'code_alpha2' => $country['code_alpha2'],
            'currency_code' => $country['currency_code'],
            'phone_code' => $country['phone_code'],
            'region' => $this->faker->randomElement(['Asia', 'Europe', 'Middle East', 'Americas']),
            'is_popular' => $this->faker->boolean(30),
            'is_active' => true,
            'hotels_count' => 0,
            'sort_order' => $this->faker->numberBetween(1, 100),
        ];
    }

    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_popular' => true,
        ]);
    }
}
