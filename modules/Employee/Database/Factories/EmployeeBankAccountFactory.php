<?php

namespace Modules\Employee\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\EmployeeBankAccount;

class EmployeeBankAccountFactory extends Factory
{
    protected $model = EmployeeBankAccount::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'bank_name' => $this->faker->randomElement([
                'Bank Asia',
                'BRAC Bank',
                'Dutch-Bangla Bank',
                'Eastern Bank',
                'Islami Bank',
                'Prime Bank',
                'City Bank',
                'Standard Chartered',
                'HSBC',
                'Sonali Bank',
            ]),
            'branch_name' => $this->faker->city.' Branch',
            'account_no' => $this->faker->unique()->numerify('##########'),
            'account_name' => $this->faker->name,
            'routing_no' => $this->faker->numerify('#########'),
            'account_type' => $this->faker->randomElement(['Savings', 'Current', 'Salary']),
        ];
    }
}
