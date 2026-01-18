<?php

namespace Modules\Flight\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Customer\Models\Customer;
use Modules\Flight\Models\FlightBooking;
use Modules\Flight\Models\FlightSupplier;

class FlightBookingFactory extends Factory
{
    protected $model = FlightBooking::class;

    public function definition(): array
    {
        $adults = $this->faker->numberBetween(1, 4);
        $children = $this->faker->numberBetween(0, 2);
        $infants = $this->faker->numberBetween(0, min($adults, 1));

        $baseFare = $this->faker->randomFloat(2, 10000, 100000);
        $tax = round($baseFare * 0.1, 2);
        $fee = round($baseFare * 0.05, 2);
        $total = $baseFare + $tax + $fee;

        return [
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
            'booking_number' => 'FLT-'.strtoupper($this->faker->unique()->bothify('??####')),
            'supplier_id' => FlightSupplier::inRandomOrder()->first()?->id,
            'customer_id' => Customer::inRandomOrder()->first()?->id,
            'trip_type' => $this->faker->randomElement(['one_way', 'round_trip']),
            'departure_date' => $this->faker->dateTimeBetween('+1 week', '+3 months'),
            'return_date' => $this->faker->optional(0.5)->dateTimeBetween('+2 weeks', '+4 months'),
            'adult_count' => $adults,
            'child_count' => $children,
            'infant_count' => $infants,
            'total_passengers' => $adults + $children + $infants,
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'processing']),
            'payment_status' => $this->faker->randomElement(['unpaid', 'partial', 'paid']),
            'ticketing_status' => $this->faker->randomElement(['not_ticketed', 'ticketed']),
            'base_fare_total' => $baseFare,
            'tax_total' => $tax,
            'fee_total' => $fee,
            'total_amount' => $total,
            'amount_paid' => $this->faker->randomFloat(2, 0, $total),
            'amount_due' => $this->faker->randomFloat(2, 0, $total),
            'currency_code' => 'BDT',
            'contact_title' => $this->faker->randomElement(['Mr', 'Mrs', 'Ms']),
            'contact_first_name' => $this->faker->firstName(),
            'contact_last_name' => $this->faker->lastName(),
            'contact_email' => $this->faker->email(),
            'contact_phone_country_code' => '+880',
            'contact_phone' => $this->faker->numerify('1#########'),
            'booking_source' => $this->faker->randomElement(['website', 'agent_portal', 'api']),
            'ip_address' => $this->faker->ipv4(),
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    public function ticketed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
            'ticketing_status' => 'ticketed',
            'payment_status' => 'paid',
            'ticketed_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }
}
