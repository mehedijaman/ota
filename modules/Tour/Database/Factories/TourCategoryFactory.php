<?php

namespace Modules\Tour\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Tour\Models\TourCategory;

class TourCategoryFactory extends Factory
{
    protected $model = TourCategory::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'Beach & Island',
            'Adventure',
            'Cultural & Heritage',
            'Wildlife & Safari',
            'Mountain & Trekking',
            'City Tours',
            'Honeymoon',
            'Family Package',
            'Pilgrimage',
            'Cruise',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraph(),
            'icon' => $this->faker->randomElement(['beach', 'mountain', 'city', 'camera', 'heart', 'users', 'compass', 'map']),
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(1, 100),
            'meta_title' => $name.' Tours',
            'meta_description' => $this->faker->sentence(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
