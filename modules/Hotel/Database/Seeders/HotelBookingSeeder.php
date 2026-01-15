<?php

namespace Modules\Hotel\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelBooking;
use Modules\Hotel\Models\HotelBookingRoom;

class HotelBookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hotels = Hotel::with('rooms')->has('rooms')->get();

        if ($hotels->isEmpty()) {
            $this->command->warn('Please run HotelSeeder first to create hotels with rooms.');

            return;
        }

        // Create 10 bookings
        for ($i = 0; $i < 10; $i++) {
            $hotel = $hotels->random();
            $room = $hotel->rooms->isNotEmpty() ? $hotel->rooms->random() : null;

            $booking = HotelBooking::factory()->create([
                'hotel_id' => $hotel->id,
            ]);

            // Create booking room if room exists
            if ($room) {
                HotelBookingRoom::create([
                    'booking_id' => $booking->id,
                    'room_id' => $room->id,
                    'rate_plan_id' => null,
                    'check_in_date' => $booking->check_in_date,
                    'check_out_date' => $booking->check_out_date,
                    'adults' => 2,
                    'children' => 0,
                    'infants' => 0,
                    'meal_plan' => 'breakfast',
                    'room_rate' => $booking->subtotal,
                    'total_amount' => $booking->subtotal,
                    'cost_amount' => $booking->cost_amount,
                    'status' => 'confirmed',
                ]);
            }
        }

        // Update hotel bookings count
        $hotels->each(function (Hotel $hotel) {
            $hotel->update([
                'bookings_count' => $hotel->bookings()->count(),
            ]);
        });

        $this->command->info('Created 10 hotel bookings.');
    }
}
