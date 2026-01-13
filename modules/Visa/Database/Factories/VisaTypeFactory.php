<?php

namespace Modules\Visa\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Visa\Models\VisaCategory;
use Modules\Visa\Models\VisaCountry;
use Modules\Visa\Models\VisaType;

class VisaTypeFactory extends Factory
{
    protected $model = VisaType::class;

    public function definition(): array
    {
        $entryType = fake()->randomElement(['single', 'double', 'multiple']);
        $validityDays = fake()->randomElement([30, 60, 90, 180, 365]);
        $stayDays = fake()->randomElement([14, 30, 60, 90]);

        return [
            'country_id' => VisaCountry::factory(),
            'category_id' => VisaCategory::factory(),
            'name' => fake()->words(3, true).' Visa',
            'slug' => Str::slug(fake()->unique()->words(4, true)),
            'code' => strtoupper(fake()->lexify('???-???')),
            'description' => fake()->paragraph(),
            'entry_type' => $entryType,
            'validity_days' => $validityDays,
            'stay_days' => $stayDays,
            'extension_days' => fake()->optional(0.3)->randomElement([30, 60, 90]),
            'is_extendable' => fake()->boolean(30),
            'is_convertible' => fake()->boolean(20),
            'work_permitted' => fake()->boolean(20),
            'study_permitted' => fake()->boolean(20),
            'min_age' => null,
            'max_age' => null,
            'eligible_nationalities' => null,
            'restricted_nationalities' => null,
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function multipleEntry(): static
    {
        return $this->state(fn (array $attributes) => [
            'entry_type' => 'multiple',
            'validity_days' => 365,
        ]);
    }
}
