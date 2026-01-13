<?php

namespace Modules\Visa\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Visa\Models\VisaCountry;

class VisaCountryFactory extends Factory
{
    protected $model = VisaCountry::class;

    protected static array $countries = [
        ['name' => 'United States', 'code' => 'US', 'continent' => 'North America', 'capital' => 'Washington D.C.', 'timezone' => 'America/New_York'],
        ['name' => 'United Kingdom', 'code' => 'GB', 'continent' => 'Europe', 'capital' => 'London', 'timezone' => 'Europe/London'],
        ['name' => 'Canada', 'code' => 'CA', 'continent' => 'North America', 'capital' => 'Ottawa', 'timezone' => 'America/Toronto'],
        ['name' => 'Australia', 'code' => 'AU', 'continent' => 'Oceania', 'capital' => 'Canberra', 'timezone' => 'Australia/Sydney'],
        ['name' => 'Singapore', 'code' => 'SG', 'continent' => 'Asia', 'capital' => 'Singapore', 'timezone' => 'Asia/Singapore'],
        ['name' => 'Malaysia', 'code' => 'MY', 'continent' => 'Asia', 'capital' => 'Kuala Lumpur', 'timezone' => 'Asia/Kuala_Lumpur'],
        ['name' => 'Thailand', 'code' => 'TH', 'continent' => 'Asia', 'capital' => 'Bangkok', 'timezone' => 'Asia/Bangkok'],
        ['name' => 'United Arab Emirates', 'code' => 'AE', 'continent' => 'Asia', 'capital' => 'Abu Dhabi', 'timezone' => 'Asia/Dubai'],
        ['name' => 'Saudi Arabia', 'code' => 'SA', 'continent' => 'Asia', 'capital' => 'Riyadh', 'timezone' => 'Asia/Riyadh'],
        ['name' => 'India', 'code' => 'IN', 'continent' => 'Asia', 'capital' => 'New Delhi', 'timezone' => 'Asia/Kolkata'],
        ['name' => 'China', 'code' => 'CN', 'continent' => 'Asia', 'capital' => 'Beijing', 'timezone' => 'Asia/Shanghai'],
        ['name' => 'Japan', 'code' => 'JP', 'continent' => 'Asia', 'capital' => 'Tokyo', 'timezone' => 'Asia/Tokyo'],
        ['name' => 'South Korea', 'code' => 'KR', 'continent' => 'Asia', 'capital' => 'Seoul', 'timezone' => 'Asia/Seoul'],
        ['name' => 'Germany', 'code' => 'DE', 'continent' => 'Europe', 'capital' => 'Berlin', 'timezone' => 'Europe/Berlin'],
        ['name' => 'France', 'code' => 'FR', 'continent' => 'Europe', 'capital' => 'Paris', 'timezone' => 'Europe/Paris'],
        ['name' => 'Italy', 'code' => 'IT', 'continent' => 'Europe', 'capital' => 'Rome', 'timezone' => 'Europe/Rome'],
        ['name' => 'Turkey', 'code' => 'TR', 'continent' => 'Asia', 'capital' => 'Ankara', 'timezone' => 'Europe/Istanbul'],
        ['name' => 'Egypt', 'code' => 'EG', 'continent' => 'Africa', 'capital' => 'Cairo', 'timezone' => 'Africa/Cairo'],
        ['name' => 'South Africa', 'code' => 'ZA', 'continent' => 'Africa', 'capital' => 'Pretoria', 'timezone' => 'Africa/Johannesburg'],
        ['name' => 'New Zealand', 'code' => 'NZ', 'continent' => 'Oceania', 'capital' => 'Wellington', 'timezone' => 'Pacific/Auckland'],
    ];

    protected static int $countryIndex = 0;

    public function definition(): array
    {
        $index = self::$countryIndex % count(self::$countries);
        $country = self::$countries[$index];
        self::$countryIndex++;

        $uniqueSuffix = self::$countryIndex > count(self::$countries)
            ? '-'.self::$countryIndex
            : '';

        return [
            'name' => $country['name'].$uniqueSuffix,
            'slug' => Str::slug($country['name']).$uniqueSuffix,
            'code' => $country['code'].$uniqueSuffix,
            'flag_icon' => null,
            'continent' => $country['continent'],
            'region' => null,
            'capital' => $country['capital'],
            'timezone' => $country['timezone'],
            'description' => fake()->paragraphs(2, true),
            'visa_info' => fake()->paragraphs(3, true),
            'embassy_name' => 'Embassy of '.$country['name'].' in Bangladesh',
            'embassy_address' => fake()->address(),
            'embassy_phone' => fake()->phoneNumber(),
            'embassy_email' => 'embassy@'.Str::slug($country['name']).'.gov',
            'embassy_website' => 'https://embassy.'.Str::slug($country['name']).'.gov',
            'embassy_working_hours' => [
                'monday' => '09:00-17:00',
                'tuesday' => '09:00-17:00',
                'wednesday' => '09:00-17:00',
                'thursday' => '09:00-17:00',
                'friday' => '09:00-17:00',
            ],
            'is_popular' => fake()->boolean(30),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 100),
            'meta_title' => $country['name'].' Visa Services',
            'meta_description' => 'Apply for '.$country['name'].' visa with our professional visa services.',
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_popular' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
