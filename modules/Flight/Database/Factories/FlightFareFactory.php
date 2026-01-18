<?php

namespace Modules\Flight\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Flight\Models\Flight;
use Modules\Flight\Models\FlightFare;
use Modules\Flight\Models\FlightFareClass;

class FlightFareFactory extends Factory
{
    protected $model = FlightFare::class;

    public function definition(): array
    {
        $basePrice = $this->faker->randomFloat(2, 5000, 30000);
        $tax = round($basePrice * 0.1, 2);
        $fee = round($basePrice * 0.05, 2);

        return [
            'uuid' => $this->faker->uuid(),
            'flight_id' => Flight::factory(),
            'fare_class_id' => FlightFareClass::factory(),
            'fare_basis_code' => strtoupper($this->faker->randomLetter().$this->faker->randomLetter().$this->faker->numberBetween(1, 99)),
            'valid_from' => now(),
            'valid_until' => now()->addMonths(3),
            'adult_base_fare' => $basePrice,
            'adult_tax' => $tax,
            'adult_fee' => $fee,
            'adult_total' => $basePrice + $tax + $fee,
            'child_base_fare' => round($basePrice * 0.75, 2),
            'child_tax' => $tax,
            'child_fee' => $fee,
            'child_total' => round($basePrice * 0.75 + $tax + $fee, 2),
            'child_discount_percent' => 25,
            'infant_base_fare' => round($basePrice * 0.1, 2),
            'infant_tax' => round($tax * 0.5, 2),
            'infant_fee' => 0,
            'infant_total' => round($basePrice * 0.1 + $tax * 0.5, 2),
            'infant_discount_percent' => 90,
            'currency_code' => 'BDT',
            'total_seats' => $this->faker->numberBetween(20, 50),
            'available_seats' => $this->faker->numberBetween(5, 20),
            'sold_seats' => $this->faker->numberBetween(0, 10),
            'net_cost' => round($basePrice * 0.85, 2),
            'markup_amount' => round($basePrice * 0.15, 2),
            'is_active' => true,
            'is_soldout' => false,
        ];
    }

    public function soldout(): static
    {
        return $this->state(fn (array $attributes) => [
            'available_seats' => 0,
            'is_soldout' => true,
        ]);
    }
}
