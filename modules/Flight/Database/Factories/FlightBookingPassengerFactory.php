<?php

namespace Modules\Flight\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Flight\Models\FlightBooking;
use Modules\Flight\Models\FlightBookingPassenger;
use Nnjeim\World\Models\Country;

class FlightBookingPassengerFactory extends Factory
{
    protected $model = FlightBookingPassenger::class;

    public function definition(): array
    {
        $passengerType = $this->faker->randomElement(['adult', 'adult', 'adult', 'child', 'infant']);
        $dob = match ($passengerType) {
            'infant' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'child' => $this->faker->dateTimeBetween('-11 years', '-2 years'),
            default => $this->faker->dateTimeBetween('-65 years', '-18 years'),
        };

        return [
            'uuid' => $this->faker->uuid(),
            'booking_id' => FlightBooking::factory(),
            'passenger_number' => 1,
            'passenger_type' => $passengerType,
            'is_lead_passenger' => true,
            'title' => $this->faker->randomElement(['Mr', 'Mrs', 'Ms', 'Miss']),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'date_of_birth' => $dob,
            'nationality_id' => Country::inRandomOrder()->first()?->id,
            'passport_number' => strtoupper($this->faker->bothify('??#######')),
            'passport_country_id' => Country::inRandomOrder()->first()?->id,
            'passport_expiry_date' => $this->faker->dateTimeBetween('+6 months', '+10 years'),
            'email' => $this->faker->email(),
            'phone' => $this->faker->phoneNumber(),
            'meal_preference' => $this->faker->randomElement(['standard', 'vegetarian', 'halal']),
            'ticket_status' => 'not_issued',
            'base_fare' => $this->faker->randomFloat(2, 5000, 20000),
            'tax' => $this->faker->randomFloat(2, 500, 2000),
            'total' => $this->faker->randomFloat(2, 6000, 25000),
        ];
    }

    public function adult(): static
    {
        return $this->state(fn (array $attributes) => [
            'passenger_type' => 'adult',
            'date_of_birth' => $this->faker->dateTimeBetween('-65 years', '-18 years'),
        ]);
    }

    public function child(): static
    {
        return $this->state(fn (array $attributes) => [
            'passenger_type' => 'child',
            'date_of_birth' => $this->faker->dateTimeBetween('-11 years', '-2 years'),
        ]);
    }

    public function infant(): static
    {
        return $this->state(fn (array $attributes) => [
            'passenger_type' => 'infant',
            'date_of_birth' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ]);
    }
}
