<?php

namespace Modules\Tour\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Tour\Models\Tour;
use Modules\Tour\Models\TourCategory;
use Modules\Tour\Models\TourDestination;

class TourFactory extends Factory
{
    protected $model = Tour::class;

    public function definition(): array
    {
        $names = [
            "Cox's Bazar Beach Getaway",
            'Sundarbans Mangrove Safari',
            'Sajek Valley Adventure',
            'Bandarban Hill Trek',
            'Saint Martin Island Escape',
            'Sylhet Tea Garden Tour',
            'Rangamati Lake Cruise',
            'Kuakata Sunrise Tour',
        ];

        $name = $this->faker->unique()->randomElement($names);
        $days = $this->faker->numberBetween(2, 7);
        $basePrice = $this->faker->numberBetween(3, 15) * 1000;

        return [
            'category_id' => TourCategory::inRandomOrder()->first()?->id,
            'destination_id' => TourDestination::inRandomOrder()->first()?->id,
            'name' => $name,
            'slug' => Str::slug($name),
            'code' => strtoupper(Str::random(3)).'-'.str_pad($this->faker->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
            'excerpt' => $this->faker->sentence(15),
            'description' => $this->faker->paragraphs(5, true),
            'duration_days' => $days,
            'duration_nights' => $days - 1,
            'min_group_size' => 1,
            'max_group_size' => $this->faker->numberBetween(10, 30),
            'difficulty_level' => $this->faker->randomElement(['easy', 'moderate', 'challenging']),
            'tour_type' => $this->faker->randomElement(['group', 'private']),
            'base_price' => $basePrice,
            'discount_price' => $this->faker->boolean(30) ? $basePrice * 0.85 : null,
            'child_price' => $basePrice * 0.6,
            'infant_price' => 0,
            'deposit_amount' => $basePrice * 0.3,
            'deposit_type' => 'fixed',
            'cancellation_days' => $this->faker->randomElement([2, 3, 5, 7]),
            'is_featured' => $this->faker->boolean(20),
            'is_bestseller' => $this->faker->boolean(15),
            'is_active' => true,
            'start_location' => 'Dhaka',
            'end_location' => 'Dhaka',
            'languages' => ['Bengali', 'English'],
            'age_restrictions' => ['min' => 0, 'max' => null],
            'meta_title' => $name,
            'meta_description' => $this->faker->sentence(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', '-1 month'),
            'updated_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    public function bestseller(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_bestseller' => true,
        ]);
    }
}
