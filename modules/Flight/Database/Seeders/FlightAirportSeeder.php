<?php

namespace Modules\Flight\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Flight\Models\FlightAirport;
use Nnjeim\World\Models\Country;

class FlightAirportSeeder extends Seeder
{
    protected array $airports = [
        // Bangladesh
        ['iata' => 'DAC', 'icao' => 'VGHS', 'name' => 'Hazrat Shahjalal International Airport', 'city' => 'Dhaka', 'country' => 'BD', 'lat' => 23.8433, 'lng' => 90.3978, 'tz' => 'Asia/Dhaka', 'type' => 'large_airport', 'intl' => true, 'hub' => true, 'popular' => true],
        ['iata' => 'CGP', 'icao' => 'VGEG', 'name' => 'Shah Amanat International Airport', 'city' => 'Chittagong', 'country' => 'BD', 'lat' => 22.2496, 'lng' => 91.8133, 'tz' => 'Asia/Dhaka', 'type' => 'medium_airport', 'intl' => true, 'hub' => false, 'popular' => true],
        ['iata' => 'ZYL', 'icao' => 'VGSY', 'name' => 'Osmani International Airport', 'city' => 'Sylhet', 'country' => 'BD', 'lat' => 24.9631, 'lng' => 91.8667, 'tz' => 'Asia/Dhaka', 'type' => 'medium_airport', 'intl' => true, 'hub' => false, 'popular' => true],
        ['iata' => 'CXB', 'icao' => 'VGCB', 'name' => "Cox's Bazar Airport", 'city' => "Cox's Bazar", 'country' => 'BD', 'lat' => 21.4522, 'lng' => 91.9639, 'tz' => 'Asia/Dhaka', 'type' => 'medium_airport', 'intl' => false, 'hub' => false, 'popular' => true],
        ['iata' => 'JSR', 'icao' => 'VGJR', 'name' => 'Jessore Airport', 'city' => 'Jessore', 'country' => 'BD', 'lat' => 23.1838, 'lng' => 89.1608, 'tz' => 'Asia/Dhaka', 'type' => 'small_airport', 'intl' => false, 'hub' => false, 'popular' => false],
        ['iata' => 'RJH', 'icao' => 'VGRJ', 'name' => 'Shah Makhdum Airport', 'city' => 'Rajshahi', 'country' => 'BD', 'lat' => 24.4372, 'lng' => 88.6164, 'tz' => 'Asia/Dhaka', 'type' => 'small_airport', 'intl' => false, 'hub' => false, 'popular' => false],

        // Middle East
        ['iata' => 'DXB', 'icao' => 'OMDB', 'name' => 'Dubai International Airport', 'city' => 'Dubai', 'country' => 'AE', 'lat' => 25.2528, 'lng' => 55.3644, 'tz' => 'Asia/Dubai', 'type' => 'large_airport', 'intl' => true, 'hub' => true, 'popular' => true],
        ['iata' => 'AUH', 'icao' => 'OMAA', 'name' => 'Abu Dhabi International Airport', 'city' => 'Abu Dhabi', 'country' => 'AE', 'lat' => 24.4539, 'lng' => 54.6511, 'tz' => 'Asia/Dubai', 'type' => 'large_airport', 'intl' => true, 'hub' => true, 'popular' => true],
        ['iata' => 'SHJ', 'icao' => 'OMSJ', 'name' => 'Sharjah International Airport', 'city' => 'Sharjah', 'country' => 'AE', 'lat' => 25.3286, 'lng' => 55.5172, 'tz' => 'Asia/Dubai', 'type' => 'large_airport', 'intl' => true, 'hub' => false, 'popular' => true],
        ['iata' => 'DOH', 'icao' => 'OTHH', 'name' => 'Hamad International Airport', 'city' => 'Doha', 'country' => 'QA', 'lat' => 25.2731, 'lng' => 51.6081, 'tz' => 'Asia/Qatar', 'type' => 'large_airport', 'intl' => true, 'hub' => true, 'popular' => true],
        ['iata' => 'RUH', 'icao' => 'OERK', 'name' => 'King Khalid International Airport', 'city' => 'Riyadh', 'country' => 'SA', 'lat' => 24.9576, 'lng' => 46.6988, 'tz' => 'Asia/Riyadh', 'type' => 'large_airport', 'intl' => true, 'hub' => true, 'popular' => true],
        ['iata' => 'JED', 'icao' => 'OEJN', 'name' => 'King Abdulaziz International Airport', 'city' => 'Jeddah', 'country' => 'SA', 'lat' => 21.6796, 'lng' => 39.1566, 'tz' => 'Asia/Riyadh', 'type' => 'large_airport', 'intl' => true, 'hub' => true, 'popular' => true],
        ['iata' => 'DMM', 'icao' => 'OEDF', 'name' => 'King Fahd International Airport', 'city' => 'Dammam', 'country' => 'SA', 'lat' => 26.4712, 'lng' => 49.7979, 'tz' => 'Asia/Riyadh', 'type' => 'large_airport', 'intl' => true, 'hub' => false, 'popular' => true],
        ['iata' => 'BAH', 'icao' => 'OBBI', 'name' => 'Bahrain International Airport', 'city' => 'Manama', 'country' => 'BH', 'lat' => 26.2708, 'lng' => 50.6336, 'tz' => 'Asia/Bahrain', 'type' => 'large_airport', 'intl' => true, 'hub' => true, 'popular' => true],
        ['iata' => 'KWI', 'icao' => 'OKBK', 'name' => 'Kuwait International Airport', 'city' => 'Kuwait City', 'country' => 'KW', 'lat' => 29.2266, 'lng' => 47.9689, 'tz' => 'Asia/Kuwait', 'type' => 'large_airport', 'intl' => true, 'hub' => true, 'popular' => true],
        ['iata' => 'MCT', 'icao' => 'OOMS', 'name' => 'Muscat International Airport', 'city' => 'Muscat', 'country' => 'OM', 'lat' => 23.5933, 'lng' => 58.2844, 'tz' => 'Asia/Muscat', 'type' => 'large_airport', 'intl' => true, 'hub' => true, 'popular' => true],

        // Asia
        ['iata' => 'SIN', 'icao' => 'WSSS', 'name' => 'Singapore Changi Airport', 'city' => 'Singapore', 'country' => 'SG', 'lat' => 1.3644, 'lng' => 103.9915, 'tz' => 'Asia/Singapore', 'type' => 'large_airport', 'intl' => true, 'hub' => true, 'popular' => true],
        ['iata' => 'KUL', 'icao' => 'WMKK', 'name' => 'Kuala Lumpur International Airport', 'city' => 'Kuala Lumpur', 'country' => 'MY', 'lat' => 2.7456, 'lng' => 101.7072, 'tz' => 'Asia/Kuala_Lumpur', 'type' => 'large_airport', 'intl' => true, 'hub' => true, 'popular' => true],
        ['iata' => 'BKK', 'icao' => 'VTBS', 'name' => 'Suvarnabhumi Airport', 'city' => 'Bangkok', 'country' => 'TH', 'lat' => 13.6900, 'lng' => 100.7501, 'tz' => 'Asia/Bangkok', 'type' => 'large_airport', 'intl' => true, 'hub' => true, 'popular' => true],
        ['iata' => 'HKG', 'icao' => 'VHHH', 'name' => 'Hong Kong International Airport', 'city' => 'Hong Kong', 'country' => 'HK', 'lat' => 22.3080, 'lng' => 113.9185, 'tz' => 'Asia/Hong_Kong', 'type' => 'large_airport', 'intl' => true, 'hub' => true, 'popular' => true],
        ['iata' => 'MLE', 'icao' => 'VRMM', 'name' => 'Velana International Airport', 'city' => 'Male', 'country' => 'MV', 'lat' => 4.1918, 'lng' => 73.5290, 'tz' => 'Indian/Maldives', 'type' => 'large_airport', 'intl' => true, 'hub' => true, 'popular' => true],
        ['iata' => 'CMB', 'icao' => 'VCBI', 'name' => 'Bandaranaike International Airport', 'city' => 'Colombo', 'country' => 'LK', 'lat' => 7.1808, 'lng' => 79.8841, 'tz' => 'Asia/Colombo', 'type' => 'large_airport', 'intl' => true, 'hub' => true, 'popular' => true],

        // India
        ['iata' => 'DEL', 'icao' => 'VIDP', 'name' => 'Indira Gandhi International Airport', 'city' => 'New Delhi', 'country' => 'IN', 'lat' => 28.5562, 'lng' => 77.1000, 'tz' => 'Asia/Kolkata', 'type' => 'large_airport', 'intl' => true, 'hub' => true, 'popular' => true],
        ['iata' => 'BOM', 'icao' => 'VABB', 'name' => 'Chhatrapati Shivaji Maharaj International Airport', 'city' => 'Mumbai', 'country' => 'IN', 'lat' => 19.0887, 'lng' => 72.8679, 'tz' => 'Asia/Kolkata', 'type' => 'large_airport', 'intl' => true, 'hub' => true, 'popular' => true],
        ['iata' => 'CCU', 'icao' => 'VECC', 'name' => 'Netaji Subhash Chandra Bose International Airport', 'city' => 'Kolkata', 'country' => 'IN', 'lat' => 22.6547, 'lng' => 88.4467, 'tz' => 'Asia/Kolkata', 'type' => 'large_airport', 'intl' => true, 'hub' => true, 'popular' => true],
        ['iata' => 'MAA', 'icao' => 'VOMM', 'name' => 'Chennai International Airport', 'city' => 'Chennai', 'country' => 'IN', 'lat' => 12.9941, 'lng' => 80.1709, 'tz' => 'Asia/Kolkata', 'type' => 'large_airport', 'intl' => true, 'hub' => true, 'popular' => true],
        ['iata' => 'BLR', 'icao' => 'VOBL', 'name' => 'Kempegowda International Airport', 'city' => 'Bangalore', 'country' => 'IN', 'lat' => 13.1979, 'lng' => 77.7063, 'tz' => 'Asia/Kolkata', 'type' => 'large_airport', 'intl' => true, 'hub' => true, 'popular' => true],

        // Europe
        ['iata' => 'LHR', 'icao' => 'EGLL', 'name' => 'Heathrow Airport', 'city' => 'London', 'country' => 'GB', 'lat' => 51.4700, 'lng' => -0.4543, 'tz' => 'Europe/London', 'type' => 'large_airport', 'intl' => true, 'hub' => true, 'popular' => true],
        ['iata' => 'CDG', 'icao' => 'LFPG', 'name' => 'Charles de Gaulle Airport', 'city' => 'Paris', 'country' => 'FR', 'lat' => 49.0097, 'lng' => 2.5478, 'tz' => 'Europe/Paris', 'type' => 'large_airport', 'intl' => true, 'hub' => true, 'popular' => true],
        ['iata' => 'FRA', 'icao' => 'EDDF', 'name' => 'Frankfurt Airport', 'city' => 'Frankfurt', 'country' => 'DE', 'lat' => 50.0379, 'lng' => 8.5622, 'tz' => 'Europe/Berlin', 'type' => 'large_airport', 'intl' => true, 'hub' => true, 'popular' => true],
        ['iata' => 'AMS', 'icao' => 'EHAM', 'name' => 'Amsterdam Schiphol Airport', 'city' => 'Amsterdam', 'country' => 'NL', 'lat' => 52.3086, 'lng' => 4.7639, 'tz' => 'Europe/Amsterdam', 'type' => 'large_airport', 'intl' => true, 'hub' => true, 'popular' => true],
        ['iata' => 'IST', 'icao' => 'LTFM', 'name' => 'Istanbul Airport', 'city' => 'Istanbul', 'country' => 'TR', 'lat' => 41.2753, 'lng' => 28.7519, 'tz' => 'Europe/Istanbul', 'type' => 'large_airport', 'intl' => true, 'hub' => true, 'popular' => true],

        // Americas
        ['iata' => 'JFK', 'icao' => 'KJFK', 'name' => 'John F Kennedy International Airport', 'city' => 'New York', 'country' => 'US', 'lat' => 40.6413, 'lng' => -73.7781, 'tz' => 'America/New_York', 'type' => 'large_airport', 'intl' => true, 'hub' => true, 'popular' => true],
        ['iata' => 'LAX', 'icao' => 'KLAX', 'name' => 'Los Angeles International Airport', 'city' => 'Los Angeles', 'country' => 'US', 'lat' => 33.9416, 'lng' => -118.4085, 'tz' => 'America/Los_Angeles', 'type' => 'large_airport', 'intl' => true, 'hub' => true, 'popular' => true],
        ['iata' => 'YYZ', 'icao' => 'CYYZ', 'name' => 'Toronto Pearson International Airport', 'city' => 'Toronto', 'country' => 'CA', 'lat' => 43.6777, 'lng' => -79.6248, 'tz' => 'America/Toronto', 'type' => 'large_airport', 'intl' => true, 'hub' => true, 'popular' => true],
    ];

    public function run(): void
    {
        foreach ($this->airports as $airport) {
            $country = Country::where('iso2', $airport['country'])->first();

            if (! $country) {
                continue;
            }

            FlightAirport::updateOrCreate(
                ['iata_code' => $airport['iata']],
                [
                    'uuid' => (string) \Illuminate\Support\Str::uuid(),
                    'country_id' => $country->id,
                    'name' => $airport['name'],
                    'icao_code' => $airport['icao'],
                    'city' => $airport['city'],
                    'latitude' => $airport['lat'],
                    'longitude' => $airport['lng'],
                    'timezone' => $airport['tz'],
                    'utc_offset_minutes' => $this->getUtcOffset($airport['tz']),
                    'type' => $airport['type'],
                    'is_international' => $airport['intl'],
                    'is_domestic' => true,
                    'is_active' => true,
                    'is_hub' => $airport['hub'],
                    'is_popular' => $airport['popular'],
                    'search_priority' => $airport['popular'] ? 100 : ($airport['hub'] ? 80 : 50),
                ]
            );
        }
    }

    protected function getUtcOffset(string $timezone): int
    {
        try {
            $tz = new \DateTimeZone($timezone);
            $dt = new \DateTime('now', $tz);

            return $tz->getOffset($dt) / 60;
        } catch (\Exception $e) {
            return 0;
        }
    }
}
