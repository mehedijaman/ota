<?php

namespace Modules\Flight\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Customer\Models\Customer;
use Modules\Flight\Models\FlightAirline;
use Modules\Flight\Models\FlightBooking;
use Modules\Flight\Models\FlightReview;

class FlightReviewFactory extends Factory
{
    protected $model = FlightReview::class;

    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid(),
            'booking_id' => FlightBooking::inRandomOrder()->first()?->id,
            'airline_id' => FlightAirline::inRandomOrder()->first()?->id ?? FlightAirline::factory(),
            'customer_id' => Customer::inRandomOrder()->first()?->id,
            'overall_rating' => $this->faker->numberBetween(3, 5),
            'comfort_rating' => $this->faker->numberBetween(3, 5),
            'staff_rating' => $this->faker->numberBetween(3, 5),
            'food_rating' => $this->faker->numberBetween(2, 5),
            'punctuality_rating' => $this->faker->numberBetween(3, 5),
            'value_rating' => $this->faker->numberBetween(3, 5),
            'title' => $this->faker->sentence(5),
            'review' => $this->faker->paragraphs(2, true),
            'pros' => $this->faker->sentence(),
            'cons' => $this->faker->optional()->sentence(),
            'travel_class' => $this->faker->randomElement(['economy', 'business']),
            'traveler_type' => $this->faker->randomElement(['solo', 'couple', 'family', 'business']),
            'travel_date' => $this->faker->dateTimeBetween('-6 months', '-1 week'),
            'helpful_count' => $this->faker->numberBetween(0, 50),
            'is_approved' => true,
            'is_featured' => $this->faker->boolean(10),
            'is_verified_purchase' => $this->faker->boolean(80),
            'approved_at' => now(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => false,
            'approved_at' => null,
        ]);
    }
}
