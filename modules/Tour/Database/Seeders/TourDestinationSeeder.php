<?php

namespace Modules\Tour\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Tour\Models\TourDestination;

class TourDestinationSeeder extends Seeder
{
    public function run(): void
    {
        $destinations = [
            ['name' => "Cox's Bazar", 'country' => 'Bangladesh', 'city' => "Cox's Bazar", 'lat' => 21.4272, 'lng' => 92.0058, 'featured' => true],
            ['name' => 'Sundarbans', 'country' => 'Bangladesh', 'city' => 'Khulna', 'lat' => 21.9497, 'lng' => 89.1833, 'featured' => true],
            ['name' => 'Sajek Valley', 'country' => 'Bangladesh', 'city' => 'Rangamati', 'lat' => 23.3817, 'lng' => 92.2936, 'featured' => true],
            ['name' => 'Bandarban', 'country' => 'Bangladesh', 'city' => 'Bandarban', 'lat' => 22.1953, 'lng' => 92.2184, 'featured' => true],
            ['name' => "Saint Martin's Island", 'country' => 'Bangladesh', 'city' => 'Teknaf', 'lat' => 20.6273, 'lng' => 92.3226, 'featured' => true],
            ['name' => 'Sylhet', 'country' => 'Bangladesh', 'city' => 'Sylhet', 'lat' => 24.8949, 'lng' => 91.8687, 'featured' => false],
            ['name' => 'Rangamati', 'country' => 'Bangladesh', 'city' => 'Rangamati', 'lat' => 22.6372, 'lng' => 92.2061, 'featured' => false],
            ['name' => 'Kuakata', 'country' => 'Bangladesh', 'city' => 'Patuakhali', 'lat' => 21.8167, 'lng' => 90.1167, 'featured' => false],
            ['name' => 'Srimangal', 'country' => 'Bangladesh', 'city' => 'Moulvibazar', 'lat' => 24.3065, 'lng' => 91.7296, 'featured' => false],
            ['name' => 'Paharpur', 'country' => 'Bangladesh', 'city' => 'Naogaon', 'lat' => 25.0311, 'lng' => 88.9765, 'featured' => false],
        ];

        foreach ($destinations as $dest) {
            TourDestination::firstOrCreate(
                ['name' => $dest['name']],
                [
                    'slug' => Str::slug($dest['name']),
                    'country' => $dest['country'],
                    'city' => $dest['city'],
                    'latitude' => $dest['lat'],
                    'longitude' => $dest['lng'],
                    'is_featured' => $dest['featured'],
                    'is_active' => true,
                ]
            );
        }
    }
}
