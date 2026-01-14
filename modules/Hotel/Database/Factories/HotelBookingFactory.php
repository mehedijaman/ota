<?php

namespace Modules\Hotel\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelBooking;

class HotelBookingFactory extends Factory
{
    protected $model = HotelBooking::class;

    public function definition(): array
    {
        $checkInDate = $this->faker->dateTimeBetween('+1 week', '+3 months');
        $nights = $this->faker->numberBetween(1, 7);
        $checkOutDate = (clone $checkInDate)->modify("+{$nights} days");
        $totalRooms = $this->faker->numberBetween(1, 3);
        $adults = $totalRooms * 2;
        $children = $this->faker->numberBetween(0, 2);

        $subtotal = $this->faker->randomFloat(2, 5000, 50000);
        $taxAmount = round($subtotal * 0.15, 2);
        $serviceFee = round($subtotal * 0.05, 2);
        $totalAmount = $subtotal + $taxAmount + $serviceFee;
        $costAmount = round($subtotal * 0.85, 2);

        return [
            'uuid' => $this->faker->uuid(),
            'booking_number' => HotelBooking::generateBookingNumber(),
            'hotel_id' => Hotel::factory(),
            'supplier_id' => null,
            'supplier_booking_id' => null,
            'customer_id' => null,
            'agent_id' => null,
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'completed']),
            'payment_status' => $this->faker->randomElement(['unpaid', 'partial', 'paid']),
            'check_in_date' => $checkInDate,
            'check_out_date' => $checkOutDate,
            'nights' => $nights,
            'total_rooms' => $totalRooms,
            'total_adults' => $adults,
            'total_children' => $children,
            'total_infants' => 0,
            'contact_title' => $this->faker->randomElement(['Mr', 'Mrs', 'Ms', 'Dr']),
            'contact_name' => $this->faker->name(),
            'contact_email' => $this->faker->safeEmail(),
            'contact_phone' => $this->faker->phoneNumber(),
            'contact_address' => $this->faker->address(),
            'contact_city' => $this->faker->city(),
            'contact_postal_code' => $this->faker->postcode(),
            'contact_country' => 'Bangladesh',
            'special_requests' => $this->faker->optional(0.3)->sentence(),
            'estimated_arrival_time' => $this->faker->time('H:i'),
            'late_checkout_requested' => $this->faker->boolean(10),
            'early_checkin_requested' => $this->faker->boolean(15),
            'subtotal' => $subtotal,
            'extras_total' => 0,
            'tax_amount' => $taxAmount,
            'service_fee' => $serviceFee,
            'discount_amount' => 0,
            'total_amount' => $totalAmount,
            'cost_amount' => $costAmount,
            'profit_amount' => $totalAmount - $costAmount,
            'currency_code' => 'BDT',
            'amount_paid' => $this->faker->randomElement([0, $totalAmount]),
            'amount_due' => 0,
            'booking_source' => $this->faker->randomElement(['website', 'admin', 'agent', 'phone']),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'amount_paid' => 0,
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'confirmed_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => 'Guest cancelled',
        ]);
    }
}
