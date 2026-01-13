<?php

namespace Modules\Visa\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Visa\Models\Visa;
use Modules\Visa\Models\VisaProcessingTime;

class VisaProcessingTimeFactory extends Factory
{
    protected $model = VisaProcessingTime::class;

    protected array $processingTypes = [
        ['name' => 'Normal Processing', 'type' => 'normal', 'min' => 10, 'max' => 15, 'fee' => 0],
        ['name' => 'Express Processing', 'type' => 'express', 'min' => 5, 'max' => 7, 'fee' => 5000],
        ['name' => 'Urgent Processing', 'type' => 'urgent', 'min' => 3, 'max' => 5, 'fee' => 10000],
        ['name' => 'Super Urgent Processing', 'type' => 'super_urgent', 'min' => 1, 'max' => 2, 'fee' => 20000],
    ];

    public function definition(): array
    {
        $processing = fake()->randomElement($this->processingTypes);

        return [
            'visa_id' => Visa::factory(),
            'name' => $processing['name'],
            'description' => fake()->sentence(),
            'type' => $processing['type'],
            'processing_days_min' => $processing['min'],
            'processing_days_max' => $processing['max'],
            'additional_fee' => $processing['fee'],
            'is_guaranteed' => $processing['type'] !== 'normal',
            'is_active' => true,
            'sort_order' => array_search($processing, $this->processingTypes) + 1,
        ];
    }

    public function normal(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Normal Processing',
            'type' => 'normal',
            'processing_days_min' => 10,
            'processing_days_max' => 15,
            'additional_fee' => 0,
            'is_guaranteed' => false,
        ]);
    }

    public function express(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Express Processing',
            'type' => 'express',
            'processing_days_min' => 5,
            'processing_days_max' => 7,
            'additional_fee' => 5000,
            'is_guaranteed' => true,
        ]);
    }

    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Urgent Processing',
            'type' => 'urgent',
            'processing_days_min' => 3,
            'processing_days_max' => 5,
            'additional_fee' => 10000,
            'is_guaranteed' => true,
        ]);
    }
}
