<?php

namespace Modules\Testimonial\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Testimonial\Models\Testimonial;

class TestimonialFactory extends Factory
{
    protected $model = Testimonial::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'designation' => fake()->jobTitle(),
            'company' => fake()->company(),
            'title' => fake()->sentence(6),
            'description' => fake()->paragraph(3),
            'is_active' => fake()->boolean(90),
            'featured' => fake()->boolean(30),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'featured' => true,
        ]);
    }
}
