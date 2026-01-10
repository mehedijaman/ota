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
        $name = 'Category ' . \Illuminate\Support\Str::uuid();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => 'A wonderful tour category description that explains exactly what this category encompasses.',
            'icon' => \Illuminate\Support\Arr::random(['beach', 'mountain', 'city', 'camera', 'heart', 'users', 'compass', 'map']),
            'is_active' => true,
            'sort_order' => rand(1, 100),
            'meta_title' => $name.' Tours',
            'meta_description' => 'Explore the best '.$name.' tours with us.',
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
