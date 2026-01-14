<?php

namespace Modules\Hotel\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Hotel\Models\HotelSupplier;

class HotelSupplierSeeder extends Seeder
{
    protected array $suppliers = [
        [
            'name' => 'Manual Inventory',
            'type' => 'manual',
            'description' => 'Directly contracted hotels managed in-house.',
        ],
        [
            'name' => 'TBO Holidays',
            'type' => 'api',
            'description' => 'Leading B2B travel platform for hotels worldwide.',
            'endpoint' => 'https://api.tbotechnology.in',
        ],
        [
            'name' => 'RateHawk',
            'type' => 'api',
            'description' => 'Global hotel distribution platform.',
            'endpoint' => 'https://api.ratehawk.com',
        ],
        [
            'name' => 'Hotelbeds',
            'type' => 'api',
            'description' => 'Bedbank for B2B hotel distribution.',
            'endpoint' => 'https://api.hotelbeds.com',
        ],
    ];

    public function run(): void
    {
        $priority = 1;

        foreach ($this->suppliers as $supplier) {
            HotelSupplier::firstOrCreate(
                ['code' => Str::slug($supplier['name'])],
                [
                    'uuid' => Str::uuid(),
                    'name' => $supplier['name'],
                    'type' => $supplier['type'],
                    'description' => $supplier['description'],
                    'api_endpoint' => $supplier['endpoint'] ?? null,
                    'api_version' => $supplier['type'] === 'api' ? 'v2' : null,
                    'is_active' => true,
                    'priority' => $priority++,
                    'commission_rate' => $supplier['type'] === 'manual' ? 0 : rand(10, 15),
                    'markup_rate' => rand(5, 12),
                    'timeout_seconds' => 30,
                    'retry_attempts' => 3,
                    'settings' => [],
                    'supported_currencies' => ['BDT', 'USD'],
                    'supported_languages' => ['en'],
                ]
            );
        }
    }
}
