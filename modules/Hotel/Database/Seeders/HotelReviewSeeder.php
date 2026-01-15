<?php

namespace Modules\Hotel\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelReview;

class HotelReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hotels = Hotel::all();

        if ($hotels->isEmpty()) {
            $this->command->warn('Please run HotelSeeder first to create hotels.');

            return;
        }

        // Create 50 reviews distributed across hotels
        $reviewCount = 50;
        for ($i = 0; $i < $reviewCount; $i++) {
            HotelReview::factory()->create([
                'hotel_id' => $hotels->random()->id,
            ]);
        }

        // Update hotel avg ratings
        $hotels->each(function (Hotel $hotel) {
            $avgRating = $hotel->reviews()->avg('overall_rating');
            $reviewsCount = $hotel->reviews()->count();
            $hotel->update([
                'avg_rating' => $avgRating ?? 0,
                'reviews_count' => $reviewsCount,
            ]);
        });

        $this->command->info('Created 50 hotel reviews.');
    }
}
