<?php

namespace Modules\Tour\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Tour\Models\Tour;
use Modules\Tour\Models\TourBooking;
use Modules\Tour\Models\TourReview;

class TourReviewSeeder extends Seeder
{
    public function run(): void
    {
        $tours = Tour::all();
        $users = User::all();
        $bookings = TourBooking::where('status', 'completed')->get();

        if ($tours->isEmpty()) {
            $this->command->warn('No tours found. Please run TourSeeder first.');

            return;
        }

        $this->command->info('Creating 10 tour reviews...');

        for ($i = 0; $i < 10; $i++) {
            $tour = $tours->random();
            $user = $users->isNotEmpty() ? $users->random() : null;
            $booking = $bookings->where('tour_id', $tour->id)->first();

            $review = TourReview::factory()
                ->state([
                    'tour_id' => $tour->id,
                    'user_id' => $user?->id,
                    'booking_id' => $booking?->id,
                    'name' => $user?->name ?? fake()->name(),
                    'email' => $user?->email ?? fake()->safeEmail(),
                    'is_verified' => $booking !== null,
                ])
                ->create();

            $this->command->line("  Created review for: {$tour->name} - Rating: {$review->rating}");
        }

        $this->command->info('Tour reviews created successfully!');
    }
}
