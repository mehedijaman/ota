<?php

namespace Modules\Tour\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Tour\Models\Tour;
use Modules\Tour\Models\TourCategory;
use Modules\Tour\Models\TourDestination;
use Modules\Tour\Models\TourInclusion;
use Modules\Tour\Models\TourItinerary;

class TourSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TourCategorySeeder::class,
            TourDestinationSeeder::class,
        ]);

        $coxsBazar = TourDestination::where('name', "Cox's Bazar")->first();
        $beachCategory = TourCategory::where('name', 'Beach & Island')->first();

        if ($coxsBazar && $beachCategory) {
            $tour = Tour::firstOrCreate(
                ['code' => 'CBX-001'],
                [
                    'category_id' => $beachCategory->id,
                    'destination_id' => $coxsBazar->id,
                    'name' => "Cox's Bazar Beach Getaway",
                    'slug' => Str::slug("Cox's Bazar Beach Getaway"),
                    'excerpt' => 'Experience the longest sea beach in the world with our curated 3-day tour package.',
                    'description' => '<p>Experience the world\'s longest natural sea beach with our carefully curated 3-day tour package. Enjoy stunning sunsets, fresh seafood, and the warm hospitality of Cox\'s Bazar.</p><p>This package includes comfortable accommodation, all meals, transportation from Dhaka, and guided tours to Himchori, Inani Beach, and the famous Marine Drive.</p>',
                    'duration_days' => 3,
                    'duration_nights' => 2,
                    'min_group_size' => 1,
                    'max_group_size' => 20,
                    'difficulty_level' => 'easy',
                    'tour_type' => 'group',
                    'base_price' => 5500,
                    'child_price' => 3500,
                    'infant_price' => 0,
                    'deposit_amount' => 2000,
                    'deposit_type' => 'fixed',
                    'cancellation_days' => 2,
                    'is_featured' => true,
                    'is_bestseller' => true,
                    'is_active' => true,
                    'start_location' => 'Dhaka',
                    'end_location' => 'Dhaka',
                    'languages' => ['Bengali', 'English'],
                ]
            );

            // Add itineraries
            $itineraries = [
                ['day' => 1, 'title' => 'Arrival & Beach Exploration', 'desc' => 'Arrive in Cox\'s Bazar, check into hotel. Evening beach walk and sunset viewing. Dinner at seaside restaurant.'],
                ['day' => 2, 'title' => 'Full Day Sightseeing', 'desc' => 'Visit Himchori Waterfall, Inani Beach, and Marine Drive. Lunch at local restaurant. Evening free for shopping.'],
                ['day' => 3, 'title' => 'Departure', 'desc' => 'Breakfast at hotel, free time for last-minute beach visits. Check out and departure to Dhaka.'],
            ];

            foreach ($itineraries as $itin) {
                TourItinerary::firstOrCreate(
                    ['tour_id' => $tour->id, 'day_number' => $itin['day']],
                    ['title' => $itin['title'], 'description' => $itin['desc'], 'meals_included' => ['breakfast', 'lunch', 'dinner']]
                );
            }

            // Add inclusions
            $inclusions = ['Round-trip transport', 'Hotel accommodation', 'All meals', 'Sightseeing tours', 'Professional guide'];
            foreach ($inclusions as $i => $inc) {
                TourInclusion::firstOrCreate(
                    ['tour_id' => $tour->id, 'type' => 'inclusion', 'title' => $inc],
                    ['sort_order' => $i]
                );
            }

            // Add exclusions
            $exclusions = ['Personal expenses', 'Travel insurance', 'Tips & gratuities'];
            foreach ($exclusions as $i => $exc) {
                TourInclusion::firstOrCreate(
                    ['tour_id' => $tour->id, 'type' => 'exclusion', 'title' => $exc],
                    ['sort_order' => $i]
                );
            }
        }
    }
}
