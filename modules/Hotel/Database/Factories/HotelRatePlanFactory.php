<?php

namespace Modules\Hotel\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelCancellationPolicy;
use Modules\Hotel\Models\HotelRatePlan;

class HotelRatePlanFactory extends Factory
{
    protected $model = HotelRatePlan::class;

    public function definition(): array
    {
        $planType = $this->faker->randomElement([
            ['name' => 'Best Available Rate', 'code' => 'BAR', 'refundable' => true],
            ['name' => 'Non-Refundable Rate', 'code' => 'NRF', 'refundable' => false],
            ['name' => 'Advance Purchase', 'code' => 'ADV', 'refundable' => false],
            ['name' => 'Flexible Rate', 'code' => 'FLX', 'refundable' => true],
            ['name' => 'Package Rate', 'code' => 'PKG', 'refundable' => true],
            ['name' => 'Corporate Rate', 'code' => 'COR', 'refundable' => true],
        ]);

        return [
            'hotel_id' => Hotel::factory(),
            'cancellation_policy_id' => HotelCancellationPolicy::inRandomOrder()->first()?->id,
            'name' => $planType['name'],
            'code' => $planType['code'].'-'.$this->faker->unique()->numberBetween(100, 999),
            'description' => "Our {$planType['name']} offers great value for your stay.",
            'meal_plan' => $this->faker->randomElement(['room_only', 'breakfast', 'half_board', 'full_board', 'all_inclusive']),
            'is_refundable' => $planType['refundable'],
            'is_pay_at_hotel' => $this->faker->boolean(30),
            'requires_prepayment' => ! $this->faker->boolean(30),
            'prepayment_percentage' => $this->faker->randomElement([0, 50, 100]),
            'advance_booking_min' => 0,
            'advance_booking_max' => 365,
            'min_nights' => $this->faker->randomElement([1, 2]),
            'max_nights' => $this->faker->randomElement([14, 30, null]),
            'min_rooms' => 1,
            'max_rooms' => 10,
            'valid_from' => now()->toDateString(),
            'valid_to' => now()->addYear()->toDateString(),
            'applicable_days' => [0, 1, 2, 3, 4, 5, 6],
            'markup_percentage' => $this->faker->randomFloat(2, 5, 20),
            'discount_percentage' => $planType['refundable'] ? 0 : $this->faker->randomFloat(2, 5, 15),
            'is_default' => false,
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(1, 100),
        ];
    }

    public function nonRefundable(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Non-Refundable Rate',
            'is_refundable' => false,
            'discount_percentage' => $this->faker->randomFloat(2, 10, 20),
        ]);
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }
}
