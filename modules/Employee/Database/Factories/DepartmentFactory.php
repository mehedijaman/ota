<?php

namespace Modules\Employee\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Employee\Models\Department;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Human Resources',
                'Finance',
                'Marketing',
                'Sales',
                'Engineering',
                'Product Development',
                'Customer Support',
                'Operations',
                'Legal',
                'Information Technology',
                'Research & Development',
                'Quality Assurance',
                'Administration',
                'Procurement',
                'Logistics',
            ]),
            'code' => $this->faker->unique()->numerify('DEP##'),
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
