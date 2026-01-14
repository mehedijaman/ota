<?php

namespace Modules\Hotel\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Hotel\Models\HotelSupplier;

class HotelSupplierFactory extends Factory
{
    protected $model = HotelSupplier::class;

    public function definition(): array
    {
        $name = $this->faker->randomElement([
            'Agoda',
            'Booking.com',
            'Expedia',
            'Hotels.com',
            'Amadeus',
            'Sabre',
            'Travelport',
            'Manual Inventory',
            'Local Partner',
        ]);

        return [
            'uuid' => $this->faker->uuid(),
            'name' => $name,
            'code' => Str::slug($name),
            'type' => $this->faker->randomElement(['api', 'xml_feed', 'manual', 'gds']),
            'api_endpoint' => $this->faker->url(),
            'api_version' => 'v'.$this->faker->randomElement(['1', '2', '3']),
            'description' => $this->faker->sentence(),
            'is_active' => true,
            'priority' => $this->faker->numberBetween(1, 100),
            'commission_rate' => $this->faker->randomFloat(2, 5, 20),
            'markup_rate' => $this->faker->randomFloat(2, 5, 15),
            'timeout_seconds' => 30,
            'retry_attempts' => 3,
            'settings' => [],
            'supported_currencies' => ['BDT', 'USD', 'EUR'],
            'supported_languages' => ['en', 'bn'],
        ];
    }

    public function manual(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'manual',
            'api_endpoint' => null,
            'api_version' => null,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
