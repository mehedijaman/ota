<?php

namespace Modules\Tour\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Tour\Models\TourBooking;
use Modules\Tour\Models\Tour;
use Modules\Tour\Models\TourPricing;
use Modules\Customer\Models\Customer;

class TourBookingFactory extends Factory
{
    protected $model = TourBooking::class;

    public function definition(): array
    {
        return [
            'booking_number' => TourBooking::generateBookingNumber(),
            'tour_id' => Tour::factory(),
            'customer_id' => Customer::factory(),
            'pricing_id' => TourPricing::factory(),
            'travel_date' => now()->addWeeks(2),
            'status' => 'pending', // pending, confirmed, cancelled, completed
            'payment_status' => 'unpaid', // unpaid, partial, paid, refunded
            'subtotal' => 1000.00,
            'extras_total' => 0.00,
            'tax_amount' => 50.00,
            'discount_amount' => 0.00,
            'total_amount' => 1050.00,
            'amount_paid' => 0.00,
            'amount_due' => 1050.00,
            'adult_count' => 2,
            'child_count' => 0,
            'infant_count' => 0,
            'contact_name' => $this->faker->name,
            'contact_email' => $this->faker->email,
            'contact_phone' => $this->faker->phoneNumber,
        ];
    }
}
