<?php

namespace Modules\Hotel\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hotel\Models\HotelRoomType;

class HotelRoomTypeSeeder extends Seeder
{
    protected array $roomTypes = [
        ['name' => 'Standard Room', 'code' => 'STD', 'description' => 'Comfortable room with essential amenities.'],
        ['name' => 'Superior Room', 'code' => 'SUP', 'description' => 'Upgraded room with additional space and amenities.'],
        ['name' => 'Deluxe Room', 'code' => 'DLX', 'description' => 'Spacious room with premium amenities and views.'],
        ['name' => 'Executive Room', 'code' => 'EXE', 'description' => 'Business-focused room with work desk and lounge access.'],
        ['name' => 'Junior Suite', 'code' => 'JRS', 'description' => 'Open-plan suite with separate living area.'],
        ['name' => 'Suite', 'code' => 'STE', 'description' => 'Separate living and bedroom areas with luxury amenities.'],
        ['name' => 'Executive Suite', 'code' => 'EXS', 'description' => 'Premium suite with exclusive benefits and services.'],
        ['name' => 'Presidential Suite', 'code' => 'PRS', 'description' => 'Our finest accommodation with maximum luxury.'],
        ['name' => 'Family Room', 'code' => 'FAM', 'description' => 'Spacious room designed for families with children.'],
        ['name' => 'Connecting Room', 'code' => 'CON', 'description' => 'Two rooms connected by an internal door.'],
        ['name' => 'Accessible Room', 'code' => 'ACS', 'description' => 'Room designed for guests with accessibility needs.'],
        ['name' => 'Villa', 'code' => 'VLA', 'description' => 'Standalone private accommodation with garden.'],
        ['name' => 'Bungalow', 'code' => 'BNG', 'description' => 'Single-story detached accommodation.'],
        ['name' => 'Cottage', 'code' => 'CTG', 'description' => 'Cozy standalone accommodation in natural setting.'],
        ['name' => 'Penthouse', 'code' => 'PNT', 'description' => 'Top-floor luxury accommodation with panoramic views.'],
    ];

    public function run(): void
    {
        $sortOrder = 1;

        foreach ($this->roomTypes as $type) {
            HotelRoomType::firstOrCreate(
                ['code' => $type['code']],
                [
                    'name' => $type['name'],
                    'description' => $type['description'],
                    'is_active' => true,
                    'sort_order' => $sortOrder++,
                ]
            );
        }
    }
}
