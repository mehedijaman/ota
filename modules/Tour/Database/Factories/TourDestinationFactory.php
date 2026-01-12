<?php

namespace Modules\Tour\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Tour\Models\TourDestination;

class TourDestinationFactory extends Factory
{
    protected $model = TourDestination::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->city.' '.uniqid(),
            'slug' => fn (array $attributes) => Str::slug($attributes['name']),
            'country' => $this->faker->country,
            'city' => $this->faker->city,
            'description' => $this->faker->paragraph(3),
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'is_featured' => $this->faker->boolean(30),
            'is_active' => true,
            'meta_title' => $this->faker->sentence(),
            'meta_description' => $this->faker->sentence(),
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
}
