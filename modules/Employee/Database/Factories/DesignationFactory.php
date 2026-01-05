<?php

namespace Modules\Employee\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Employee\Models\Designation;

class DesignationFactory extends Factory
{
    protected $model = Designation::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Chief Executive Officer',
                'Chief Operating Officer',
                'Chief Financial Officer',
                'Chief Technology Officer',
                'Chief Marketing Officer',
                'Senior Vice President',
                'Vice President',
                'Director of Operations',
                'Director of Human Resources',
                'Director of Sales',
                'Marketing Manager',
                'Sales Manager',
                'Product Manager',
                'Project Manager',
                'Business Analyst',
                'Software Engineer',
                'Data Analyst',
                'Accountant',
                'HR Specialist',
                'Customer Service Representative',
            ]),
            'code' => $this->faker->unique()->numerify('######'),
            'is_active' => $this->faker->boolean(99),
            'created_by' => User::inRandomOrder()->first()->id ?? null,
            'updated_by' => User::inRandomOrder()->first()->id ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
