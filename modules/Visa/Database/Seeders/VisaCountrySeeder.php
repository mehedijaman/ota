<?php

namespace Modules\Visa\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Visa\Models\VisaCountry;

class VisaCountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['name' => 'United States', 'code' => 'US', 'continent' => 'North America', 'capital' => 'Washington D.C.', 'timezone' => 'America/New_York', 'is_popular' => true],
            ['name' => 'United Kingdom', 'code' => 'GB', 'continent' => 'Europe', 'capital' => 'London', 'timezone' => 'Europe/London', 'is_popular' => true],
            ['name' => 'Canada', 'code' => 'CA', 'continent' => 'North America', 'capital' => 'Ottawa', 'timezone' => 'America/Toronto', 'is_popular' => true],
            ['name' => 'Australia', 'code' => 'AU', 'continent' => 'Oceania', 'capital' => 'Canberra', 'timezone' => 'Australia/Sydney', 'is_popular' => true],
            ['name' => 'Singapore', 'code' => 'SG', 'continent' => 'Asia', 'capital' => 'Singapore', 'timezone' => 'Asia/Singapore', 'is_popular' => true],
            ['name' => 'Malaysia', 'code' => 'MY', 'continent' => 'Asia', 'capital' => 'Kuala Lumpur', 'timezone' => 'Asia/Kuala_Lumpur', 'is_popular' => true],
            ['name' => 'Thailand', 'code' => 'TH', 'continent' => 'Asia', 'capital' => 'Bangkok', 'timezone' => 'Asia/Bangkok', 'is_popular' => true],
            ['name' => 'United Arab Emirates', 'code' => 'AE', 'continent' => 'Asia', 'capital' => 'Abu Dhabi', 'timezone' => 'Asia/Dubai', 'is_popular' => true],
            ['name' => 'Saudi Arabia', 'code' => 'SA', 'continent' => 'Asia', 'capital' => 'Riyadh', 'timezone' => 'Asia/Riyadh', 'is_popular' => true],
            ['name' => 'India', 'code' => 'IN', 'continent' => 'Asia', 'capital' => 'New Delhi', 'timezone' => 'Asia/Kolkata', 'is_popular' => true],
        ];

        foreach ($countries as $index => $country) {
            VisaCountry::firstOrCreate(
                ['code' => $country['code']],
                [
                    'name' => $country['name'],
                    'slug' => Str::slug($country['name']),
                    'continent' => $country['continent'],
                    'capital' => $country['capital'],
                    'timezone' => $country['timezone'],
                    'description' => 'Visa services for '.$country['name'].'. We provide comprehensive visa assistance for all types of visas.',
                    'visa_info' => 'Apply for '.$country['name'].' visa with our professional services. We handle tourist, business, work, and student visa applications.',
                    'embassy_name' => 'Embassy of '.$country['name'].' in Bangladesh',
                    'embassy_address' => 'Diplomatic Enclave, Dhaka, Bangladesh',
                    'embassy_phone' => '+880-2-XXXXXXXX',
                    'embassy_email' => 'embassy@'.Str::slug($country['name']).'.gov',
                    'embassy_website' => 'https://embassy.'.Str::slug($country['name']).'.gov',
                    'embassy_working_hours' => [
                        'monday' => '09:00-17:00',
                        'tuesday' => '09:00-17:00',
                        'wednesday' => '09:00-17:00',
                        'thursday' => '09:00-17:00',
                        'friday' => '09:00-17:00',
                    ],
                    'is_popular' => $country['is_popular'],
                    'is_active' => true,
                    'sort_order' => $index + 1,
                    'meta_title' => $country['name'].' Visa Services | Apply Online',
                    'meta_description' => 'Apply for '.$country['name'].' visa online. Fast processing, expert guidance, and high success rate.',
                ]
            );
        }
    }
}
