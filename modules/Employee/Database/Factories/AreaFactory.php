<?php

namespace Modules\Employee\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Employee\Models\Area;

class AreaFactory extends Factory
{
    protected $model = Area::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Head Office',
                'Branch Office',
                'Regional Office',
                'Factory Floor',
                'Warehouse',
                'Distribution Center',
                'Remote',
                'Field Operations',
                'Customer Site',
                'Data Center',
                'Research Lab',
                'Training Center',
                'Call Center',
                'Retail Store',
                'Service Center',
            ]),
            'code' => $this->faker->unique()->numerify('AR##'),
            'is_active' => $this->faker->boolean(95),
            'created_by' => User::inRandomOrder()->first()?->id,
            'updated_by' => User::inRandomOrder()->first()?->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
