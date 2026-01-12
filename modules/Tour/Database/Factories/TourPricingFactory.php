<?php

namespace Modules\Tour\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Tour\Models\Tour;
use Modules\Tour\Models\TourPricing;

class TourPricingFactory extends Factory
{
    protected $model = TourPricing::class;

    public function definition(): array
    {
        return [
            'tour_id' => Tour::factory(),
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'adult_price' => $this->faker->randomFloat(2, 100, 1000),
            'child_price' => $this->faker->randomFloat(2, 50, 500),
            'infant_price' => $this->faker->randomFloat(2, 0, 100),
            'is_active' => true,
        ];
    }
}
