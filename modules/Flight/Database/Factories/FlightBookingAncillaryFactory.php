<?php

namespace Modules\Flight\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Flight\Models\FlightBooking;
use Modules\Flight\Models\FlightBookingAncillary;
use Modules\Flight\Models\FlightBookingPassenger;
use Modules\Flight\Models\FlightBookingSegment;

class FlightBookingAncillaryFactory extends Factory
{
    protected $model = FlightBookingAncillary::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['baggage', 'meal', 'lounge', 'priority_boarding']);

        return [
            'booking_id' => FlightBooking::factory(),
            'segment_id' => FlightBookingSegment::factory(),
            'passenger_id' => FlightBookingPassenger::factory(),
            'type' => $type,
            'name' => $this->getNameForType($type),
            'description' => $this->getDescriptionForType($type),
            'quantity' => $type === 'baggage' ? $this->faker->numberBetween(1, 2) : 1,
            'weight_kg' => $type === 'baggage' ? $this->faker->randomElement([20, 23, 25, 30]) : null,
            'unit_price' => $this->getPriceForType($type),
            'total_price' => $this->getPriceForType($type),
            'currency_code' => 'BDT',
            'status' => 'confirmed',
        ];
    }

    protected function getNameForType(string $type): string
    {
        return match ($type) {
            'baggage' => 'Extra Checked Bag',
            'meal' => $this->faker->randomElement(['Halal Meal', 'Vegetarian Meal', 'Child Meal']),
            'lounge' => 'Airport Lounge Access',
            'priority_boarding' => 'Priority Boarding',
            default => 'Service',
        };
    }

    protected function getDescriptionForType(string $type): string
    {
        return match ($type) {
            'baggage' => 'Additional checked baggage allowance',
            'meal' => 'Special meal request',
            'lounge' => 'Access to departure lounge',
            'priority_boarding' => 'Board the aircraft first',
            default => '',
        };
    }

    protected function getPriceForType(string $type): float
    {
        return match ($type) {
            'baggage' => $this->faker->randomElement([2500, 3500, 5000]),
            'meal' => $this->faker->randomElement([500, 750, 1000]),
            'lounge' => $this->faker->randomElement([2000, 3000, 5000]),
            'priority_boarding' => $this->faker->randomElement([500, 750]),
            default => 0,
        };
    }
}
