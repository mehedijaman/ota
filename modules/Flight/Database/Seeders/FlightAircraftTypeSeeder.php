<?php

namespace Modules\Flight\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Flight\Models\FlightAircraftType;

class FlightAircraftTypeSeeder extends Seeder
{
    protected array $aircraft = [
        // Boeing Narrow Body
        ['name' => 'Boeing 737-800', 'iata' => '738', 'icao' => 'B738', 'manufacturer' => 'Boeing', 'model' => '737', 'variant' => '800', 'category' => 'narrow_body', 'engine_type' => 'jet', 'engine_count' => 2, 'seats' => 189, 'range' => 5665, 'speed' => 850],
        ['name' => 'Boeing 737 MAX 8', 'iata' => '7M8', 'icao' => 'B38M', 'manufacturer' => 'Boeing', 'model' => '737 MAX', 'variant' => '8', 'category' => 'narrow_body', 'engine_type' => 'jet', 'engine_count' => 2, 'seats' => 178, 'range' => 6570, 'speed' => 842],
        ['name' => 'Boeing 737-900ER', 'iata' => '739', 'icao' => 'B739', 'manufacturer' => 'Boeing', 'model' => '737', 'variant' => '900ER', 'category' => 'narrow_body', 'engine_type' => 'jet', 'engine_count' => 2, 'seats' => 220, 'range' => 5925, 'speed' => 850],

        // Boeing Wide Body
        ['name' => 'Boeing 777-300ER', 'iata' => '77W', 'icao' => 'B77W', 'manufacturer' => 'Boeing', 'model' => '777', 'variant' => '300ER', 'category' => 'wide_body', 'engine_type' => 'jet', 'engine_count' => 2, 'seats' => 396, 'range' => 13650, 'speed' => 905],
        ['name' => 'Boeing 777-200LR', 'iata' => '77L', 'icao' => 'B77L', 'manufacturer' => 'Boeing', 'model' => '777', 'variant' => '200LR', 'category' => 'wide_body', 'engine_type' => 'jet', 'engine_count' => 2, 'seats' => 317, 'range' => 15843, 'speed' => 905],
        ['name' => 'Boeing 787-8 Dreamliner', 'iata' => '788', 'icao' => 'B788', 'manufacturer' => 'Boeing', 'model' => '787', 'variant' => '8', 'category' => 'wide_body', 'engine_type' => 'jet', 'engine_count' => 2, 'seats' => 242, 'range' => 13530, 'speed' => 913],
        ['name' => 'Boeing 787-9 Dreamliner', 'iata' => '789', 'icao' => 'B789', 'manufacturer' => 'Boeing', 'model' => '787', 'variant' => '9', 'category' => 'wide_body', 'engine_type' => 'jet', 'engine_count' => 2, 'seats' => 290, 'range' => 14140, 'speed' => 913],
        ['name' => 'Boeing 747-8', 'iata' => '748', 'icao' => 'B748', 'manufacturer' => 'Boeing', 'model' => '747', 'variant' => '8', 'category' => 'wide_body', 'engine_type' => 'jet', 'engine_count' => 4, 'seats' => 467, 'range' => 14815, 'speed' => 920],

        // Airbus Narrow Body
        ['name' => 'Airbus A320', 'iata' => '320', 'icao' => 'A320', 'manufacturer' => 'Airbus', 'model' => 'A320', 'variant' => 'ceo', 'category' => 'narrow_body', 'engine_type' => 'jet', 'engine_count' => 2, 'seats' => 180, 'range' => 6100, 'speed' => 840],
        ['name' => 'Airbus A320neo', 'iata' => '32N', 'icao' => 'A20N', 'manufacturer' => 'Airbus', 'model' => 'A320', 'variant' => 'neo', 'category' => 'narrow_body', 'engine_type' => 'jet', 'engine_count' => 2, 'seats' => 180, 'range' => 6500, 'speed' => 840],
        ['name' => 'Airbus A321neo', 'iata' => '32Q', 'icao' => 'A21N', 'manufacturer' => 'Airbus', 'model' => 'A321', 'variant' => 'neo', 'category' => 'narrow_body', 'engine_type' => 'jet', 'engine_count' => 2, 'seats' => 220, 'range' => 7400, 'speed' => 840],
        ['name' => 'Airbus A319', 'iata' => '319', 'icao' => 'A319', 'manufacturer' => 'Airbus', 'model' => 'A319', 'variant' => 'ceo', 'category' => 'narrow_body', 'engine_type' => 'jet', 'engine_count' => 2, 'seats' => 144, 'range' => 6950, 'speed' => 840],

        // Airbus Wide Body
        ['name' => 'Airbus A330-300', 'iata' => '333', 'icao' => 'A333', 'manufacturer' => 'Airbus', 'model' => 'A330', 'variant' => '300', 'category' => 'wide_body', 'engine_type' => 'jet', 'engine_count' => 2, 'seats' => 295, 'range' => 11750, 'speed' => 871],
        ['name' => 'Airbus A350-900', 'iata' => '359', 'icao' => 'A359', 'manufacturer' => 'Airbus', 'model' => 'A350', 'variant' => '900', 'category' => 'wide_body', 'engine_type' => 'jet', 'engine_count' => 2, 'seats' => 325, 'range' => 15000, 'speed' => 903],
        ['name' => 'Airbus A350-1000', 'iata' => '351', 'icao' => 'A35K', 'manufacturer' => 'Airbus', 'model' => 'A350', 'variant' => '1000', 'category' => 'wide_body', 'engine_type' => 'jet', 'engine_count' => 2, 'seats' => 369, 'range' => 16100, 'speed' => 903],
        ['name' => 'Airbus A380-800', 'iata' => '388', 'icao' => 'A388', 'manufacturer' => 'Airbus', 'model' => 'A380', 'variant' => '800', 'category' => 'wide_body', 'engine_type' => 'jet', 'engine_count' => 4, 'seats' => 525, 'range' => 15700, 'speed' => 903],

        // Regional
        ['name' => 'Bombardier Dash 8 Q400', 'iata' => 'DH4', 'icao' => 'DH8D', 'manufacturer' => 'Bombardier', 'model' => 'Dash 8', 'variant' => 'Q400', 'category' => 'turboprop', 'engine_type' => 'turboprop', 'engine_count' => 2, 'seats' => 78, 'range' => 2040, 'speed' => 667],
        ['name' => 'ATR 72-600', 'iata' => 'AT7', 'icao' => 'AT76', 'manufacturer' => 'ATR', 'model' => 'ATR 72', 'variant' => '600', 'category' => 'turboprop', 'engine_type' => 'turboprop', 'engine_count' => 2, 'seats' => 72, 'range' => 1528, 'speed' => 511],
        ['name' => 'Embraer E195-E2', 'iata' => 'E95', 'icao' => 'E295', 'manufacturer' => 'Embraer', 'model' => 'E195', 'variant' => 'E2', 'category' => 'regional_jet', 'engine_type' => 'jet', 'engine_count' => 2, 'seats' => 132, 'range' => 4800, 'speed' => 870],
    ];

    public function run(): void
    {
        foreach ($this->aircraft as $aircraft) {
            FlightAircraftType::updateOrCreate(
                ['iata_code' => $aircraft['iata']],
                [
                    'uuid' => (string) Str::uuid(),
                    'name' => $aircraft['name'],
                    'icao_code' => $aircraft['icao'],
                    'manufacturer' => $aircraft['manufacturer'],
                    'model' => $aircraft['model'],
                    'variant' => $aircraft['variant'],
                    'category' => $aircraft['category'],
                    'engine_type' => $aircraft['engine_type'],
                    'engine_count' => $aircraft['engine_count'],
                    'typical_seats' => $aircraft['seats'],
                    'max_seats' => (int) ($aircraft['seats'] * 1.1),
                    'economy_class_seats' => (int) ($aircraft['seats'] * 0.85),
                    'business_class_seats' => (int) ($aircraft['seats'] * 0.12),
                    'first_class_seats' => (int) ($aircraft['seats'] * 0.03),
                    'range_km' => $aircraft['range'],
                    'cruise_speed_kmh' => $aircraft['speed'],
                    'is_active' => true,
                ]
            );
        }
    }
}
