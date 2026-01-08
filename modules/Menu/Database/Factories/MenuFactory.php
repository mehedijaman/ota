<?php

namespace Modules\Menu\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Menu\Models\Menu;

class MenuFactory extends Factory
{
    protected $model = Menu::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
            'position' => $this->faker->randomElement(['primary', 'footer', 'sidebar']),
            'active' => true,
            'created_at' => $this->faker->dateTimeBetween('-1 year', '-6 month'),
            'updated_at' => $this->faker->dateTimeBetween('-5 month', 'now'),
        ];
    }

    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Primary Menu',
            'position' => 'primary',
        ]);
    }

    public function footer(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Footer Menu',
            'position' => 'footer',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }
}
