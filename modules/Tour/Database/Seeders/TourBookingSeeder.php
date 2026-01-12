<?php

namespace Modules\Tour\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Customer\Models\Customer;
use Modules\Tour\Models\Tour;
use Modules\Tour\Models\TourBooking;

class TourBookingSeeder extends Seeder
{
    public function run(): void
    {
        $tours = Tour::with('pricings')->get();
        $customers = Customer::all();

        if ($tours->isEmpty()) {
            $this->command->warn('No tours found. Please run TourSeeder first.');

            return;
        }

        if ($customers->isEmpty()) {
            $this->command->warn('No customers found. Creating bookings without customer reference.');
        }

        $this->command->info('Creating 10 tour bookings...');

        for ($i = 0; $i < 10; $i++) {
            $tour = $tours->random();
            $pricing = $tour->pricings->first();
            $customer = $customers->isNotEmpty() ? $customers->random() : null;

            $booking = TourBooking::factory()
                ->state([
                    'tour_id' => $tour->id,
                    'pricing_id' => $pricing?->id,
                    'customer_id' => $customer?->id,
                    'contact_name' => $customer?->full_name ?? fake()->name(),
                    'contact_email' => $customer?->email ?? fake()->safeEmail(),
                    'contact_phone' => $customer?->phone ?? fake()->phoneNumber(),
                ])
                ->create();

            $this->command->line("  Created booking: {$booking->booking_number} for {$tour->name}");
        }

        $this->command->info('Tour bookings created successfully!');
    }
}
