<?php

namespace Modules\Flight\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Flight\Models\FlightSupplier;

class FlightSupplierFactory extends Factory
{
    protected $model = FlightSupplier::class;

    protected array $supplierNames = [
        'Amadeus' => 'gds',
        'Sabre' => 'gds',
        'Travelport' => 'gds',
        'Kiwi.com' => 'aggregator',
        'Duffel' => 'ndc',
        'TripJack' => 'consolidator',
        'Air Arabia' => 'lcc_api',
        'IndiGo' => 'lcc_api',
    ];

    public function definition(): array
    {
        $nameType = $this->faker->randomElement(array_keys($this->supplierNames));
        $type = $this->supplierNames[$nameType];
        $code = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $nameType), 0, 3));

        return [
            'uuid' => $this->faker->uuid(),
            'name' => $nameType,
            'code' => $code.'-'.$this->faker->unique()->numberBetween(100, 999),
            'type' => $type,
            'api_base_url' => $this->faker->url(),
            'api_version' => $this->faker->randomElement(['v1', 'v2', 'v3']),
            'api_credentials' => [
                'api_key' => 'test_'.$this->faker->sha256(),
                'api_secret' => 'test_'.$this->faker->sha256(),
            ],
            'api_settings' => [
                'timeout' => 30,
                'retry' => 3,
            ],
            'auth_type' => $this->faker->randomElement(['basic', 'oauth2', 'api_key']),
            'is_active' => true,
            'is_production' => false,
            'priority' => $this->faker->numberBetween(1, 100),
            'timeout_seconds' => 30,
            'max_retries' => 3,
            'default_commission_rate' => $this->faker->randomFloat(2, 5, 15),
            'default_markup_rate' => $this->faker->randomFloat(2, 3, 10),
            'commission_type' => 'percentage',
            'currency_code' => 'BDT',
            'supported_features' => ['booking', 'cancel', 'refund'],
            'contact_name' => $this->faker->name(),
            'contact_email' => $this->faker->companyEmail(),
            'contact_phone' => $this->faker->phoneNumber(),
            'support_email' => 'support@'.strtolower(preg_replace('/[^a-zA-Z]/', '', $nameType)).'.com',
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function production(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_production' => true,
        ]);
    }

    public function gds(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'gds',
        ]);
    }
}
