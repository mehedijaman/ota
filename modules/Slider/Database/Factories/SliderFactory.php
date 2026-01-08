<?php

namespace Modules\Slider\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Slider\Models\Slider;

class SliderFactory extends Factory
{
    protected $model = Slider::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'url' => $this->faker->url,
            'button_text' => $this->faker->randomElement(['Learn More', 'Get Started', 'Read More', 'Contact Us']),
            'order' => $this->faker->numberBetween(1, 10),
            'is_active' => $this->faker->boolean(90),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
