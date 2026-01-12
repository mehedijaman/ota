<?php

namespace Modules\Tour\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Tour\Models\Tour;
use Modules\Tour\Models\TourBooking;
use Modules\Tour\Models\TourReview;

class TourReviewFactory extends Factory
{
    protected $model = TourReview::class;

    protected array $reviewTitles = [
        'Amazing experience!',
        'Highly recommended tour',
        'Great value for money',
        'Wonderful trip',
        'Exceeded expectations',
        'Beautiful destination',
        'Professional service',
        'Unforgettable journey',
        'Perfect vacation',
        'Best tour ever',
    ];

    protected array $positiveContent = [
        'The tour was absolutely fantastic! Our guide was knowledgeable and friendly.',
        'Everything was well organized from start to finish. Would definitely book again.',
        'Beautiful scenery and great accommodations. The food was excellent too.',
        'Our family had an amazing time. The kids especially loved the activities.',
        'Professional team, comfortable transport, and stunning locations.',
        'The itinerary was perfect - not too rushed, with enough time to explore.',
        'Value for money is excellent. All inclusions were exactly as described.',
        'The local experiences made this tour special. Authentic and memorable.',
        'Clean and comfortable hotels, delicious meals, and helpful staff.',
        'This was our first tour with them and we are already planning the next one.',
    ];

    protected array $pros = [
        'Well organized itinerary',
        'Knowledgeable tour guide',
        'Comfortable transportation',
        'Good quality hotels',
        'Delicious food',
        'Scenic locations',
        'Value for money',
        'Friendly staff',
        'Clean facilities',
        'Punctual service',
    ];

    protected array $cons = [
        'Some walking required',
        'Early morning starts',
        'Limited free time',
        'Crowded during peak season',
        'Long travel distances',
    ];

    public function definition(): array
    {
        $rating = $this->faker->randomFloat(1, 3.5, 5.0);
        $isApproved = $this->faker->boolean(80);

        return [
            'tour_id' => Tour::factory(),
            'user_id' => User::factory(),
            'booking_id' => null,
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'rating' => $rating,
            'title' => $this->faker->randomElement($this->reviewTitles),
            'content' => $this->faker->randomElement($this->positiveContent),
            'pros' => $this->faker->randomElements($this->pros, $this->faker->numberBetween(2, 5)),
            'cons' => $rating < 4.5 ? $this->faker->randomElements($this->cons, $this->faker->numberBetween(1, 2)) : [],
            'is_verified' => $this->faker->boolean(60),
            'is_approved' => $isApproved,
            'is_featured' => $isApproved && $this->faker->boolean(20),
            'travel_date' => $this->faker->dateTimeBetween('-6 months', '-1 week'),
            'helpful_count' => $this->faker->numberBetween(0, 50),
        ];
    }

    /**
     * Mark review as verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
        ]);
    }

    /**
     * Mark review as approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => true,
        ]);
    }

    /**
     * Mark review as featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'is_approved' => true,
        ]);
    }

    /**
     * Attach to a booking.
     */
    public function withBooking(): static
    {
        return $this->state(fn (array $attributes) => [
            'booking_id' => TourBooking::factory(),
            'is_verified' => true,
        ]);
    }
}
