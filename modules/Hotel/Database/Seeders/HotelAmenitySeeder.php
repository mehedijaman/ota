<?php

namespace Modules\Hotel\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hotel\Models\HotelAmenity;

class HotelAmenitySeeder extends Seeder
{
    protected array $amenities = [
        // Connectivity
        ['name' => 'Free WiFi', 'slug' => 'wifi', 'icon' => 'wifi', 'category' => 'internet', 'popular' => true],
        ['name' => 'Business Center', 'slug' => 'business-center', 'icon' => 'briefcase', 'category' => 'business', 'popular' => false],
        ['name' => 'Meeting Rooms', 'slug' => 'meeting-room', 'icon' => 'users', 'category' => 'business', 'popular' => false],

        // Recreation
        ['name' => 'Swimming Pool', 'slug' => 'pool', 'icon' => 'swimming-pool', 'category' => 'pool_beach', 'popular' => true],
        ['name' => 'Fitness Center', 'slug' => 'gym', 'icon' => 'dumbbell', 'category' => 'wellness_spa', 'popular' => true],
        ['name' => 'Spa', 'slug' => 'spa', 'icon' => 'spa', 'category' => 'wellness_spa', 'popular' => true],
        ['name' => 'Sauna', 'slug' => 'sauna', 'icon' => 'hot-tub', 'category' => 'wellness_spa', 'popular' => false],

        // Dining
        ['name' => 'Restaurant', 'slug' => 'restaurant', 'icon' => 'utensils', 'category' => 'food_drink', 'popular' => true],
        ['name' => 'Bar/Lounge', 'slug' => 'bar', 'icon' => 'cocktail', 'category' => 'food_drink', 'popular' => false],
        ['name' => 'Room Service', 'slug' => 'room-service', 'icon' => 'concierge-bell', 'category' => 'services', 'popular' => true],
        ['name' => 'Breakfast Included', 'slug' => 'breakfast', 'icon' => 'coffee', 'category' => 'food_drink', 'popular' => true],

        // Transportation
        ['name' => 'Free Parking', 'slug' => 'parking', 'icon' => 'parking', 'category' => 'parking', 'popular' => true],
        ['name' => 'Airport Shuttle', 'slug' => 'airport-shuttle', 'icon' => 'shuttle-van', 'category' => 'transportation', 'popular' => true],
        ['name' => 'Car Rental', 'slug' => 'car-rental', 'icon' => 'car', 'category' => 'transportation', 'popular' => false],

        // Services
        ['name' => '24-Hour Front Desk', 'slug' => '24hr-front-desk', 'icon' => 'clock', 'category' => 'services', 'popular' => true],
        ['name' => 'Concierge', 'slug' => 'concierge', 'icon' => 'concierge-bell', 'category' => 'services', 'popular' => false],
        ['name' => 'Laundry Service', 'slug' => 'laundry', 'icon' => 'tshirt', 'category' => 'services', 'popular' => true],
        ['name' => 'Luggage Storage', 'slug' => 'luggage-storage', 'icon' => 'suitcase', 'category' => 'services', 'popular' => false],

        // Accessibility
        ['name' => 'Wheelchair Accessible', 'slug' => 'wheelchair', 'icon' => 'wheelchair', 'category' => 'accessibility', 'popular' => false],
        ['name' => 'Elevator', 'slug' => 'elevator', 'icon' => 'building', 'category' => 'accessibility', 'popular' => false],

        // Family
        ['name' => 'Kids Club', 'slug' => 'kids-club', 'icon' => 'child', 'category' => 'family', 'popular' => false],
        ['name' => 'Babysitting', 'slug' => 'babysitting', 'icon' => 'baby', 'category' => 'family', 'popular' => false],

        // Others
        ['name' => 'Pet Friendly', 'slug' => 'pet-friendly', 'icon' => 'paw', 'category' => 'general', 'popular' => false],
        ['name' => 'Smoking Rooms', 'slug' => 'smoking-rooms', 'icon' => 'smoking', 'category' => 'room', 'popular' => false],
        ['name' => 'Non-Smoking Rooms', 'slug' => 'non-smoking', 'icon' => 'smoking-ban', 'category' => 'room', 'popular' => true],
    ];

    public function run(): void
    {
        $sortOrder = 1;

        foreach ($this->amenities as $amenity) {
            HotelAmenity::firstOrCreate(
                ['slug' => $amenity['slug']],
                [
                    'name' => $amenity['name'],
                    'icon' => $amenity['icon'],
                    'category' => $amenity['category'],
                    'is_popular' => $amenity['popular'],
                    'is_active' => true,
                    'sort_order' => $amenity['popular'] ? $sortOrder++ : 100 + $sortOrder++,
                ]
            );
        }
    }
}
