<?php

namespace Modules\Flight\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Flight\Models\FlightAirline;
use Modules\Flight\Models\FlightFareClass;

class FlightFareClassFactory extends Factory
{
    protected $model = FlightFareClass::class;

    public function definition(): array
    {
        $cabinClass = $this->faker->randomElement(['economy', 'premium_economy', 'business', 'first']);

        return [
            'uuid' => $this->faker->uuid(),
            'airline_id' => FlightAirline::factory(),
            'code' => strtoupper($this->faker->randomLetter()).$this->faker->unique()->numberBetween(1, 99),
            'name' => $this->getNameForCabin($cabinClass),
            'booking_class' => $this->faker->randomElement(['Y', 'B', 'M', 'H', 'K', 'L', 'V', 'S', 'N', 'Q', 'O']),
            'cabin_class' => $cabinClass,
            'fare_type' => $this->faker->randomElement(['published', 'private', 'promotional']),
            'brand_name' => $this->getBrandName($cabinClass),
            'checked_bags_included' => $cabinClass === 'economy' ? $this->faker->randomElement([0, 1]) : $this->faker->randomElement([1, 2]),
            'carry_on_bags_included' => 1,
            'checked_bag_weight_kg' => $cabinClass === 'economy' ? 23 : 32,
            'carry_on_weight_kg' => 7,
            'is_refundable' => $cabinClass !== 'economy' || $this->faker->boolean(30),
            'is_changeable' => true,
            'change_fee' => $cabinClass === 'economy' ? $this->faker->randomElement([3000, 5000, 7500]) : 0,
            'cancellation_fee' => $cabinClass === 'economy' ? $this->faker->randomElement([5000, 7500, 10000]) : 0,
            'seat_selection_free' => $cabinClass !== 'economy',
            'priority_boarding' => $cabinClass !== 'economy',
            'lounge_access' => in_array($cabinClass, ['business', 'first']),
            'mileage_earning_percent' => $this->getMileagePercent($cabinClass),
            'display_order' => $this->faker->numberBetween(1, 100),
            'is_active' => true,
        ];
    }

    protected function getNameForCabin(string $cabin): string
    {
        return match ($cabin) {
            'first' => $this->faker->randomElement(['First Class', 'Suites', 'First']),
            'business' => $this->faker->randomElement(['Business Class', 'Business', 'Business Plus']),
            'premium_economy' => $this->faker->randomElement(['Premium Economy', 'Economy Plus', 'Economy Comfort']),
            default => $this->faker->randomElement(['Economy', 'Economy Standard', 'Economy Lite', 'Economy Flex']),
        };
    }

    protected function getBrandName(string $cabin): string
    {
        return match ($cabin) {
            'first' => $this->faker->randomElement(['Diamond', 'Platinum', 'Royal']),
            'business' => $this->faker->randomElement(['Sapphire', 'Executive', 'Club']),
            'premium_economy' => $this->faker->randomElement(['Premium', 'Plus', 'Comfort']),
            default => $this->faker->randomElement(['Value', 'Smart', 'Saver', 'Flex']),
        };
    }

    protected function getMileagePercent(string $cabin): int
    {
        return match ($cabin) {
            'first' => 150,
            'business' => 125,
            'premium_economy' => 100,
            default => $this->faker->randomElement([25, 50, 75, 100]),
        };
    }

    public function economy(): static
    {
        return $this->state(fn (array $attributes) => [
            'cabin_class' => 'economy',
            'name' => 'Economy',
        ]);
    }

    public function business(): static
    {
        return $this->state(fn (array $attributes) => [
            'cabin_class' => 'business',
            'name' => 'Business Class',
            'is_refundable' => true,
            'lounge_access' => true,
        ]);
    }
}
