<?php

namespace Modules\Tour\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Tour\Models\TourBookingTraveler;
use Modules\Tour\Models\TourBooking;

class TourBookingTravelerFactory extends Factory
{
    protected $model = TourBookingTraveler::class;

    public function definition(): array
    {
        return [
            'booking_id' => TourBooking::factory(),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'date_of_birth' => $this->faker->date('Y-m-d', '-25 years'),
            'type' => 'adult', // adult, child, infant
            'is_lead' => false,
            'passport_number' => $this->faker->regexify('[A-Z0-9]{9}'),
            'passport_expiry' => $this->faker->date('Y-m-d', '+5 years'),
        ];
    }
}
