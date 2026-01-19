<?php

namespace Modules\Hotel\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Hotel\Models\HotelCity;
use Nnjeim\World\Models\Country;

class HotelCitySeeder extends Seeder
{
    protected array $bangladeshCities = [
        ['name' => 'Dhaka', 'lat' => 23.8103, 'lng' => 90.4125, 'iata' => 'DAC', 'popular' => true],
        ['name' => 'Chittagong', 'lat' => 22.3569, 'lng' => 91.7832, 'iata' => 'CGP', 'popular' => true],
        ['name' => "Cox's Bazar", 'lat' => 21.4272, 'lng' => 92.0058, 'iata' => 'CXB', 'popular' => true],
        ['name' => 'Sylhet', 'lat' => 24.8949, 'lng' => 91.8687, 'iata' => 'ZYL', 'popular' => true],
        ['name' => 'Khulna', 'lat' => 22.8456, 'lng' => 89.5403, 'iata' => null, 'popular' => false],
        ['name' => 'Rajshahi', 'lat' => 24.3636, 'lng' => 88.6241, 'iata' => 'RJH', 'popular' => false],
        ['name' => 'Rangpur', 'lat' => 25.7439, 'lng' => 89.2752, 'iata' => null, 'popular' => false],
        ['name' => 'Barisal', 'lat' => 22.7010, 'lng' => 90.3535, 'iata' => 'BZL', 'popular' => false],
        ['name' => 'Comilla', 'lat' => 23.4607, 'lng' => 91.1809, 'iata' => null, 'popular' => false],
        ['name' => 'Bandarban', 'lat' => 22.1953, 'lng' => 92.2184, 'iata' => null, 'popular' => true],
        ['name' => 'Sajek', 'lat' => 23.3782, 'lng' => 92.2939, 'iata' => null, 'popular' => true],
        ['name' => 'Sreemangal', 'lat' => 24.3063, 'lng' => 91.7296, 'iata' => null, 'popular' => false],
        ['name' => 'Saint Martin', 'lat' => 20.6276, 'lng' => 92.3226, 'iata' => null, 'popular' => true],
        ['name' => 'Sundarbans', 'lat' => 21.9497, 'lng' => 89.1833, 'iata' => null, 'popular' => false],
        ['name' => 'Gazipur', 'lat' => 24.0022, 'lng' => 90.4264, 'iata' => null, 'popular' => false],
    ];

    public function run(): void
    {
        // Use the World package's Country model
        $bangladesh = Country::where('iso2', 'BD')->orWhere('iso3', 'BGD')->first();

        if (! $bangladesh) {
            $this->command->warn('Bangladesh country not found. Please run world:install first.');

            return;
        }

        $sortOrder = 1;

        foreach ($this->bangladeshCities as $city) {
            HotelCity::firstOrCreate(
                ['slug' => Str::slug($city['name']), 'country_id' => $bangladesh->id],
                [
                    'name' => $city['name'],
                    'country_id' => $bangladesh->id,
                    'latitude' => $city['lat'],
                    'longitude' => $city['lng'],
                    'timezone' => 'Asia/Dhaka',
                    'iata_code' => $city['iata'],
                    'description' => "Explore the beautiful city of {$city['name']} with our hotel partners.",
                    'is_popular' => $city['popular'],
                    'is_active' => true,
                    'hotels_count' => 0,
                    'sort_order' => $city['popular'] ? $sortOrder++ : 100 + $sortOrder++,
                    'meta_title' => "Hotels in {$city['name']}, Bangladesh",
                    'meta_description' => "Find and book the best hotels in {$city['name']}. Compare prices and get the best deals.",
                ]
            );
        }

        $this->command->info('Created '.count($this->bangladeshCities).' hotel cities.');
    }
}
