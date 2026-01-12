<?php

namespace Modules\Tour\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Tour\Models\TourBooking;
use Modules\Tour\Models\TourBookingExtra;
use Modules\Tour\Models\TourExtra;

class TourBookingExtraFactory extends Factory
{
    protected $model = TourBookingExtra::class;

    public function definition(): array
    {
        return [
            'booking_id' => TourBooking::factory(),
            'extra_id' => TourExtra::factory(),
            'unit_price' => $this->faker->randomFloat(2, 10, 100),
            'quantity' => 1,
            'total_price' => function (array $attributes) {
                return $attributes['unit_price'] * $attributes['quantity'];
            },
        ];
    }
}
