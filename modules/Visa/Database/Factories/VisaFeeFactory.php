<?php

namespace Modules\Visa\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Visa\Models\Visa;
use Modules\Visa\Models\VisaFee;

class VisaFeeFactory extends Factory
{
    protected $model = VisaFee::class;

    public function definition(): array
    {
        $feeType = fake()->randomElement(['embassy', 'service', 'vfs', 'courier', 'insurance', 'express', 'other']);

        $amounts = [
            'embassy' => fake()->randomElement([5000, 8000, 10000, 15000, 20000]),
            'service' => fake()->randomElement([2000, 3000, 5000, 7500]),
            'vfs' => fake()->randomElement([1500, 2000, 2500, 3000]),
            'courier' => fake()->randomElement([500, 800, 1000, 1500]),
            'insurance' => fake()->randomElement([1500, 2000, 3000, 5000]),
            'express' => fake()->randomElement([3000, 5000, 8000, 10000]),
            'other' => fake()->randomElement([500, 1000, 2000]),
        ];

        return [
            'visa_id' => Visa::factory(),
            'name' => ucfirst($feeType).' Fee',
            'description' => fake()->sentence(),
            'fee_type' => $feeType,
            'applicant_type' => 'all',
            'nationality_code' => null,
            'amount' => $amounts[$feeType],
            'is_per_person' => true,
            'is_refundable' => $feeType === 'service',
            'is_included_in_package' => in_array($feeType, ['service', 'vfs']),
            'is_optional' => in_array($feeType, ['express', 'courier', 'insurance']),
            'is_active' => true,
            'valid_from' => now()->subMonths(6),
            'valid_to' => now()->addYear(),
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }

    public function embassy(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Embassy Fee',
            'fee_type' => 'embassy',
            'is_refundable' => false,
        ]);
    }

    public function service(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Service Fee',
            'fee_type' => 'service',
            'is_refundable' => true,
        ]);
    }

    public function express(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Express Processing Fee',
            'fee_type' => 'express',
            'is_optional' => true,
        ]);
    }
}
