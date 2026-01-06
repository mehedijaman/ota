<?php

namespace Modules\Agent\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Agent\Models\Agent;

class AgentFactory extends Factory
{
    protected $model = Agent::class;

    public function definition(): array
    {
        return [
            'company_name' => $this->faker->company(),
            'established_at' => $this->faker->dateTimeBetween('-10 years', '-1 year'),
            'contact_person' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'postal_code' => $this->faker->postcode(),
            'country' => $this->faker->country(),
            'trade_license_no' => $this->faker->optional()->numerify('TL-#####'),
            'mocat_no' => $this->faker->optional()->numerify('MC-#####'),
            'nid_no' => $this->faker->optional()->numerify('##########'),
            'tin_no' => $this->faker->optional()->numerify('TIN-########'),
            'status' => $this->faker->randomElement(['pending', 'approved', 'suspended', 'inactive']),
            'remarks' => $this->faker->optional()->sentence(),
            'balance' => $this->faker->randomFloat(2, 0, 100000),
            'credit_limit' => $this->faker->randomFloat(2, 0, 500000),
            'created_at' => $this->faker->dateTimeBetween('-1 year', '-6 month'),
            'updated_at' => $this->faker->dateTimeBetween('-5 month', 'now'),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'suspended',
        ]);
    }
}
