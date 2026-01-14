<?php

namespace Modules\Hotel\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hotel\Models\HotelRoomAmenity;

class HotelRoomAmenitySeeder extends Seeder
{
    protected array $amenities = [
        // Climate
        ['name' => 'Air Conditioning', 'slug' => 'ac', 'icon' => 'snowflake', 'category' => 'climate'],
        ['name' => 'Heating', 'slug' => 'heating', 'icon' => 'temperature-high', 'category' => 'climate'],
        ['name' => 'Fan', 'slug' => 'fan', 'icon' => 'wind', 'category' => 'climate'],

        // Entertainment
        ['name' => 'Flat Screen TV', 'slug' => 'tv', 'icon' => 'tv', 'category' => 'entertainment'],
        ['name' => 'Cable/Satellite TV', 'slug' => 'cable-tv', 'icon' => 'satellite-dish', 'category' => 'entertainment'],
        ['name' => 'Streaming Services', 'slug' => 'streaming', 'icon' => 'play', 'category' => 'entertainment'],

        // Technology
        ['name' => 'Free WiFi', 'slug' => 'wifi', 'icon' => 'wifi', 'category' => 'technology'],
        ['name' => 'Work Desk', 'slug' => 'desk', 'icon' => 'desktop', 'category' => 'furniture'],
        ['name' => 'USB Charging Ports', 'slug' => 'usb', 'icon' => 'plug', 'category' => 'technology'],

        // Bathroom
        ['name' => 'Private Bathroom', 'slug' => 'private-bath', 'icon' => 'bath', 'category' => 'bathroom'],
        ['name' => 'Shower', 'slug' => 'shower', 'icon' => 'shower', 'category' => 'bathroom'],
        ['name' => 'Bathtub', 'slug' => 'bathtub', 'icon' => 'bath', 'category' => 'bathroom'],
        ['name' => 'Hair Dryer', 'slug' => 'hairdryer', 'icon' => 'wind', 'category' => 'bathroom'],
        ['name' => 'Toiletries', 'slug' => 'toiletries', 'icon' => 'soap', 'category' => 'bathroom'],
        ['name' => 'Bathrobes', 'slug' => 'bathrobes', 'icon' => 'tshirt', 'category' => 'bathroom'],
        ['name' => 'Slippers', 'slug' => 'slippers', 'icon' => 'shoe-prints', 'category' => 'bathroom'],

        // Kitchen
        ['name' => 'Minibar', 'slug' => 'minibar', 'icon' => 'wine-bottle', 'category' => 'kitchen'],
        ['name' => 'Coffee/Tea Maker', 'slug' => 'coffee', 'icon' => 'coffee', 'category' => 'kitchen'],
        ['name' => 'Electric Kettle', 'slug' => 'kettle', 'icon' => 'mug-hot', 'category' => 'kitchen'],

        // Safety
        ['name' => 'Safe', 'slug' => 'safe', 'icon' => 'lock', 'category' => 'safety'],

        // Furniture
        ['name' => 'Iron & Ironing Board', 'slug' => 'iron', 'icon' => 'tshirt', 'category' => 'furniture'],
        ['name' => 'Wardrobe', 'slug' => 'wardrobe', 'icon' => 'door-closed', 'category' => 'furniture'],

        // Bedding
        ['name' => 'Pillow Menu', 'slug' => 'pillow-menu', 'icon' => 'bed', 'category' => 'bed_linen'],
        ['name' => 'Premium Bedding', 'slug' => 'premium-bedding', 'icon' => 'bed', 'category' => 'bed_linen'],

        // Other
        ['name' => 'Balcony', 'slug' => 'balcony', 'icon' => 'door-open', 'category' => 'other'],
        ['name' => 'City View', 'slug' => 'city-view', 'icon' => 'city', 'category' => 'other'],
        ['name' => 'Sea View', 'slug' => 'sea-view', 'icon' => 'water', 'category' => 'other'],
        ['name' => 'Garden View', 'slug' => 'garden-view', 'icon' => 'tree', 'category' => 'other'],
        ['name' => 'Pool View', 'slug' => 'pool-view', 'icon' => 'swimming-pool', 'category' => 'other'],
    ];

    public function run(): void
    {
        $sortOrder = 1;

        foreach ($this->amenities as $amenity) {
            HotelRoomAmenity::firstOrCreate(
                ['slug' => $amenity['slug']],
                [
                    'name' => $amenity['name'],
                    'icon' => $amenity['icon'],
                    'category' => $amenity['category'],
                    'is_active' => true,
                    'sort_order' => $sortOrder++,
                ]
            );
        }
    }
}
