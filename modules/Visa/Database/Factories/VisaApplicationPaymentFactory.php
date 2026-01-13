<?php

namespace Modules\Visa\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Visa\Models\VisaApplication;
use Modules\Visa\Models\VisaApplicationPayment;

class VisaApplicationPaymentFactory extends Factory
{
    protected $model = VisaApplicationPayment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['pending', 'processing', 'completed', 'failed']);
        $paidAt = $status === 'completed' ? $this->faker->dateTimeThisMonth() : null;
        $failedAt = $status === 'failed' ? $this->faker->dateTimeThisMonth() : null;

        return [
            'application_id' => VisaApplication::factory(),
            'transaction_id' => 'TXN-'.strtoupper($this->faker->unique()->bothify('########')),
            'payment_method' => $this->faker->randomElement(['bkash', 'nagad', 'card', 'bank', 'cash']),
            'payment_gateway' => $this->faker->randomElement(['sslcommerz', 'bkash', 'manual', null]),
            'gateway_transaction_id' => $this->faker->optional()->uuid(),
            'amount' => $this->faker->numberBetween(5000, 100000),
            'status' => $status,
            'type' => $this->faker->randomElement(['deposit', 'partial', 'full']),
            'paid_at' => $paidAt,
            'failed_at' => $failedAt,
            'failure_reason' => $failedAt ? $this->faker->sentence() : null,
            'notes' => $this->faker->optional()->sentence(),
            'receipt_number' => $status === 'completed' ? 'RCP-'.$this->faker->unique()->numerify('######') : null,
        ];
    }

    /**
     * Indicate payment is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'paid_at' => null,
            'failed_at' => null,
            'receipt_number' => null,
        ]);
    }

    /**
     * Indicate payment is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'paid_at' => now(),
            'failed_at' => null,
            'receipt_number' => 'RCP-'.$this->faker->unique()->numerify('######'),
        ]);
    }

    /**
     * Indicate payment is failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'paid_at' => null,
            'failed_at' => now(),
            'failure_reason' => $this->faker->sentence(),
            'receipt_number' => null,
        ]);
    }

    /**
     * Indicate payment is via bKash.
     */
    public function bkash(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'bkash',
            'payment_gateway' => 'bkash',
        ]);
    }

    /**
     * Indicate payment is via SSLCommerz.
     */
    public function sslcommerz(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'card',
            'payment_gateway' => 'sslcommerz',
        ]);
    }

    /**
     * Indicate payment is manual.
     */
    public function manual(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'cash',
            'payment_gateway' => 'manual',
        ]);
    }
}
