<?php

namespace Modules\Hotel\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hotel\Models\HotelCountry;

class HotelCountrySeeder extends Seeder
{
    protected array $countries = [
        ['name' => 'Bangladesh', 'code' => 'BGD', 'code_alpha2' => 'BD', 'currency_code' => 'BDT', 'phone_code' => '+880', 'region' => 'Asia', 'popular' => true],
        ['name' => 'India', 'code' => 'IND', 'code_alpha2' => 'IN', 'currency_code' => 'INR', 'phone_code' => '+91', 'region' => 'Asia', 'popular' => true],
        ['name' => 'Thailand', 'code' => 'THA', 'code_alpha2' => 'TH', 'currency_code' => 'THB', 'phone_code' => '+66', 'region' => 'Asia', 'popular' => true],
        ['name' => 'Malaysia', 'code' => 'MYS', 'code_alpha2' => 'MY', 'currency_code' => 'MYR', 'phone_code' => '+60', 'region' => 'Asia', 'popular' => true],
        ['name' => 'Singapore', 'code' => 'SGP', 'code_alpha2' => 'SG', 'currency_code' => 'SGD', 'phone_code' => '+65', 'region' => 'Asia', 'popular' => true],
        ['name' => 'United Arab Emirates', 'code' => 'ARE', 'code_alpha2' => 'AE', 'currency_code' => 'AED', 'phone_code' => '+971', 'region' => 'Middle East', 'popular' => true],
        ['name' => 'Saudi Arabia', 'code' => 'SAU', 'code_alpha2' => 'SA', 'currency_code' => 'SAR', 'phone_code' => '+966', 'region' => 'Middle East', 'popular' => true],
        ['name' => 'United Kingdom', 'code' => 'GBR', 'code_alpha2' => 'GB', 'currency_code' => 'GBP', 'phone_code' => '+44', 'region' => 'Europe', 'popular' => false],
        ['name' => 'United States', 'code' => 'USA', 'code_alpha2' => 'US', 'currency_code' => 'USD', 'phone_code' => '+1', 'region' => 'Americas', 'popular' => false],
        ['name' => 'Nepal', 'code' => 'NPL', 'code_alpha2' => 'NP', 'currency_code' => 'NPR', 'phone_code' => '+977', 'region' => 'Asia', 'popular' => false],
        ['name' => 'Maldives', 'code' => 'MDV', 'code_alpha2' => 'MV', 'currency_code' => 'MVR', 'phone_code' => '+960', 'region' => 'Asia', 'popular' => true],
        ['name' => 'Sri Lanka', 'code' => 'LKA', 'code_alpha2' => 'LK', 'currency_code' => 'LKR', 'phone_code' => '+94', 'region' => 'Asia', 'popular' => false],
        ['name' => 'Turkey', 'code' => 'TUR', 'code_alpha2' => 'TR', 'currency_code' => 'TRY', 'phone_code' => '+90', 'region' => 'Europe', 'popular' => true],
        ['name' => 'Indonesia', 'code' => 'IDN', 'code_alpha2' => 'ID', 'currency_code' => 'IDR', 'phone_code' => '+62', 'region' => 'Asia', 'popular' => true],
        ['name' => 'Vietnam', 'code' => 'VNM', 'code_alpha2' => 'VN', 'currency_code' => 'VND', 'phone_code' => '+84', 'region' => 'Asia', 'popular' => false],
    ];

    public function run(): void
    {
        $sortOrder = 1;

        foreach ($this->countries as $country) {
            HotelCountry::firstOrCreate(
                ['code' => $country['code']],
                [
                    'name' => $country['name'],
                    'code_alpha2' => $country['code_alpha2'],
                    'currency_code' => $country['currency_code'],
                    'phone_code' => $country['phone_code'],
                    'region' => $country['region'],
                    'is_popular' => $country['popular'],
                    'is_active' => true,
                    'hotels_count' => 0,
                    'sort_order' => $country['popular'] ? $sortOrder++ : 100 + $sortOrder++,
                ]
            );
        }
    }
}
