<?php

namespace Modules\Tour\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Customer\Models\Customer;
use Modules\Tour\Models\Tour;
use Modules\Tour\Models\TourBooking;
use Modules\Tour\Models\TourPricing;

class TourBookingFactory extends Factory
{
    protected $model = TourBooking::class;

    public function definition(): array
    {
        $adultCount = $this->faker->numberBetween(1, 4);
        $childCount = $this->faker->numberBetween(0, 2);
        $infantCount = $this->faker->boolean(30) ? $this->faker->numberBetween(0, 1) : 0;

        $adultPrice = $this->faker->randomElement([5000, 8000, 10000, 12000, 15000, 20000]);
        $childPrice = round($adultPrice * 0.6);
        $infantPrice = round($adultPrice * 0.2);

        $subtotal = ($adultCount * $adultPrice) + ($childCount * $childPrice) + ($infantCount * $infantPrice);
        $extrasTotal = $this->faker->boolean(40) ? $this->faker->randomElement([500, 1000, 1500, 2000]) : 0;
        $taxAmount = round($subtotal * 0.05);
        $discountAmount = $this->faker->boolean(30) ? $this->faker->randomElement([500, 1000, 2000]) : 0;
        $totalAmount = $subtotal + $extrasTotal + $taxAmount - $discountAmount;

        $status = $this->faker->randomElement(['pending', 'pending', 'processing', 'confirmed', 'confirmed', 'confirmed', 'completed', 'completed', 'cancelled']);
        $paymentStatus = match ($status) {
            'completed' => 'paid',
            'confirmed' => $this->faker->randomElement(['paid', 'partial']),
            'cancelled' => $this->faker->randomElement(['unpaid', 'refunded']),
            default => $this->faker->randomElement(['unpaid', 'unpaid', 'partial']),
        };

        $amountPaid = match ($paymentStatus) {
            'paid' => $totalAmount,
            'partial' => round($totalAmount * $this->faker->randomFloat(2, 0.3, 0.7)),
            'refunded' => 0,
            default => 0,
        };
        $amountDue = max(0, $totalAmount - $amountPaid);

        return [
            'booking_number' => TourBooking::generateBookingNumber(),
            'tour_id' => Tour::factory(),
            'customer_id' => Customer::factory(),
            'pricing_id' => TourPricing::factory(),
            'travel_date' => $this->faker->dateTimeBetween('now', '+3 months'),
            'status' => $status,
            'payment_status' => $paymentStatus,
            'adult_count' => $adultCount,
            'child_count' => $childCount,
            'infant_count' => $infantCount,
            'contact_name' => $this->faker->name(),
            'contact_email' => $this->faker->safeEmail(),
            'contact_phone' => $this->faker->phoneNumber(),
            'whatsapp' => $this->faker->boolean(70) ? $this->faker->phoneNumber() : null,
            'contact_address' => $this->faker->address(),
            'emergency_contact' => $this->faker->boolean(50) ? $this->faker->name() : null,
            'emergency_phone' => $this->faker->boolean(50) ? $this->faker->phoneNumber() : null,
            'special_requests' => $this->faker->boolean(30) ? $this->faker->sentence() : null,
            'dietary_requirements' => $this->faker->boolean(20) ? $this->faker->randomElement(['Vegetarian', 'Vegan', 'Halal', 'No seafood', 'Gluten-free']) : null,
            'medical_conditions' => $this->faker->boolean(10) ? $this->faker->sentence() : null,
            'subtotal' => $subtotal,
            'extras_total' => $extrasTotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'discount_code' => $discountAmount > 0 ? strtoupper($this->faker->lexify('????##')) : null,
            'total_amount' => $totalAmount,
            'amount_paid' => $amountPaid,
            'amount_due' => $amountDue,
            'booking_source' => $this->faker->randomElement(['website', 'website', 'admin', 'agent']),
            'internal_notes' => $this->faker->boolean(20) ? $this->faker->sentence() : null,
            'confirmed_at' => in_array($status, ['confirmed', 'completed']) ? $this->faker->dateTimeBetween('-2 months', 'now') : null,
            'cancelled_at' => $status === 'cancelled' ? $this->faker->dateTimeBetween('-1 month', 'now') : null,
            'cancellation_reason' => $status === 'cancelled' ? $this->faker->randomElement(['Changed plans', 'Found better deal', 'Personal reasons', 'Weather concerns']) : null,
        ];
    }

    /**
     * Set status as confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'amount_paid' => $attributes['total_amount'],
            'amount_due' => 0,
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Set status as completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'payment_status' => 'paid',
            'amount_paid' => $attributes['total_amount'],
            'amount_due' => 0,
            'confirmed_at' => now()->subDays(30),
            'travel_date' => now()->subDays(7),
        ]);
    }

    /**
     * Set status as pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'amount_paid' => 0,
            'amount_due' => $attributes['total_amount'],
        ]);
    }

    /**
     * Set status as cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'payment_status' => 'refunded',
            'cancelled_at' => now(),
            'cancellation_reason' => 'Cancelled by customer',
        ]);
    }
}
