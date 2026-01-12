<?php

namespace Modules\Tour\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Tour\Models\Tour;
use Modules\Tour\Models\TourExtra;

class TourExtraFactory extends Factory
{
    protected $model = TourExtra::class;

    public function definition(): array
    {
        return [
            'tour_id' => Tour::factory(),
            'name' => $this->faker->words(3, true),
            'price' => $this->faker->randomFloat(2, 10, 100),
            'price_type' => 'per_person', // per_person, per_booking
            'is_active' => true,
        ];
    }
}
