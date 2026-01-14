<?php

namespace Modules\Hotel\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelReview;

class HotelReviewFactory extends Factory
{
    protected $model = HotelReview::class;

    protected array $reviewTitles = [
        'Excellent stay!',
        'Great experience',
        'Highly recommended',
        'Perfect vacation',
        'Amazing hotel',
        'Wonderful hospitality',
        'Good value for money',
        'Nice place to stay',
        'Comfortable rooms',
        'Beautiful property',
        'Average experience',
        'Could be better',
    ];

    public function definition(): array
    {
        $overallRating = $this->faker->numberBetween(3, 5);

        return [
            'uuid' => $this->faker->uuid(),
            'hotel_id' => Hotel::factory(),
            'booking_id' => null,
            'customer_id' => null,
            'overall_rating' => $overallRating,
            'cleanliness_rating' => $this->faker->numberBetween(max(1, $overallRating - 1), 5),
            'service_rating' => $this->faker->numberBetween(max(1, $overallRating - 1), 5),
            'location_rating' => $this->faker->numberBetween(max(1, $overallRating - 1), 5),
            'value_rating' => $this->faker->numberBetween(max(1, $overallRating - 1), 5),
            'facilities_rating' => $this->faker->numberBetween(max(1, $overallRating - 1), 5),
            'comfort_rating' => $this->faker->numberBetween(max(1, $overallRating - 1), 5),
            'food_rating' => $this->faker->optional(0.7)->numberBetween(3, 5),
            'title' => $this->faker->randomElement($this->reviewTitles),
            'content' => $this->generateReviewContent($overallRating),
            'pros' => $this->generatePros($overallRating),
            'cons' => $this->generateCons($overallRating),
            'reviewer_name' => $this->faker->name(),
            'reviewer_country' => $this->faker->country(),
            'travel_type' => $this->faker->randomElement(['business', 'leisure', 'family', 'couple', 'solo']),
            'stayed_in' => $this->faker->dateTimeBetween('-6 months', '-1 week'),
            'nights_stayed' => $this->faker->numberBetween(1, 7),
            'room_type_stayed' => $this->faker->randomElement(['Deluxe Room', 'Superior Room', 'Standard Room', 'Suite']),
            'is_verified' => $this->faker->boolean(80),
            'is_published' => true,
            'is_featured' => $this->faker->boolean(10),
            'is_anonymous' => $this->faker->boolean(15),
            'helpful_count' => $this->faker->numberBetween(0, 50),
            'not_helpful_count' => $this->faker->numberBetween(0, 10),
            'reported_count' => 0,
            'hotel_response' => $this->faker->optional(0.3)->paragraph(),
            'hotel_responded_at' => $this->faker->optional(0.3)->dateTimeBetween('-1 month', 'now'),
            'moderation_status' => 'approved',
            'moderated_at' => now(),
        ];
    }

    protected function generateReviewContent(int $rating): string
    {
        if ($rating >= 4) {
            return 'We had an amazing stay at this hotel. The staff was incredibly friendly and helpful. '.
                'The room was clean and comfortable. '.$this->faker->paragraph(2);
        } elseif ($rating === 3) {
            return 'Overall, our stay was satisfactory. The hotel met our basic expectations. '.
                $this->faker->paragraph(2);
        }

        return 'The stay was below our expectations. '.$this->faker->paragraph(2);
    }

    protected function generatePros(int $rating): ?string
    {
        if ($rating < 3) {
            return null;
        }

        $pros = $this->faker->randomElements([
            'Excellent location',
            'Friendly staff',
            'Clean rooms',
            'Great breakfast',
            'Comfortable beds',
            'Good WiFi',
            'Beautiful view',
            'Nice pool',
            'Value for money',
        ], $this->faker->numberBetween(2, 5));

        return implode(', ', $pros);
    }

    protected function generateCons(int $rating): ?string
    {
        if ($rating >= 5) {
            return null;
        }

        $cons = $this->faker->randomElements([
            'Slow WiFi',
            'Small bathroom',
            'Limited parking',
            'Noisy at night',
            'Breakfast could be better',
            'Old furniture',
            'Slow check-in',
        ], $this->faker->numberBetween(1, 3));

        return implode(', ', $cons);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'overall_rating' => 5,
        ]);
    }

    public function pendingModeration(): static
    {
        return $this->state(fn (array $attributes) => [
            'moderation_status' => 'pending',
            'is_published' => false,
            'moderated_at' => null,
        ]);
    }
}
