<?php

namespace Modules\Testimonial\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Testimonial\Models\Testimonial;

class TestimonialFactory extends Factory
{
    protected $model = Testimonial::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->sentence(4);

        return [
            'name' => $this->faker->name,
            'designation' => $this->faker->jobTitle,
            'company' => $this->faker->company,
            'title' => $this->faker->sentence(6), // Short title
            'description' => $this->faker->paragraph(3), // Random text for the testimonial
            'image' => $this->faker->imageUrl(200, 200, 'people', true, 'Testimonial'), // Generates a placeholder image
            'active' => $this->faker->boolean(90), // 90% chance of being true
            'featured' => $this->faker->boolean(30), // 30% chance of being true

            'created_at' => $this->faker->dateTimeBetween('-1 year', '-6 month'),
            'updated_at' => $this->faker->dateTimeBetween('-5 month', 'now'),
        ];
    }
}
