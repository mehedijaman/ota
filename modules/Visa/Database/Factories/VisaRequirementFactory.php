<?php

namespace Modules\Visa\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Visa\Models\Visa;
use Modules\Visa\Models\VisaRequirement;

class VisaRequirementFactory extends Factory
{
    protected $model = VisaRequirement::class;

    protected array $requirements = [
        ['name' => 'Valid Passport', 'type' => 'document', 'mandatory' => true],
        ['name' => 'Passport Size Photo', 'type' => 'document', 'mandatory' => true],
        ['name' => 'Bank Statement (6 months)', 'type' => 'financial', 'mandatory' => true],
        ['name' => 'Employment Letter', 'type' => 'document', 'mandatory' => false],
        ['name' => 'Business Registration', 'type' => 'document', 'mandatory' => false],
        ['name' => 'Travel Insurance', 'type' => 'document', 'mandatory' => false],
        ['name' => 'Flight Reservation', 'type' => 'document', 'mandatory' => false],
        ['name' => 'Hotel Booking', 'type' => 'document', 'mandatory' => false],
        ['name' => 'Invitation Letter', 'type' => 'document', 'mandatory' => false],
        ['name' => 'Travel Itinerary', 'type' => 'information', 'mandatory' => true],
        ['name' => 'National ID Card', 'type' => 'document', 'mandatory' => true],
        ['name' => 'Tax Return Certificate', 'type' => 'financial', 'mandatory' => false],
        ['name' => 'Marriage Certificate', 'type' => 'document', 'mandatory' => false],
        ['name' => 'Birth Certificate', 'type' => 'document', 'mandatory' => false],
        ['name' => 'Police Clearance', 'type' => 'document', 'mandatory' => false],
    ];

    public function definition(): array
    {
        $requirement = fake()->randomElement($this->requirements);

        return [
            'visa_id' => Visa::factory(),
            'name' => $requirement['name'],
            'slug' => Str::slug($requirement['name']),
            'type' => $requirement['type'],
            'description' => fake()->sentence(),
            'instructions' => fake()->paragraph(),
            'sample_format' => null,
            'is_mandatory' => $requirement['mandatory'],
            'needs_upload' => true,
            'needs_original' => fake()->boolean(30),
            'needs_notarization' => fake()->boolean(10),
            'needs_translation' => fake()->boolean(20),
            'needs_attestation' => fake()->boolean(15),
            'copies_required' => fake()->randomElement([1, 2]),
            'accepted_formats' => ['pdf', 'jpg', 'jpeg', 'png'],
            'max_file_size_kb' => 5120,
            'applicant_type' => 'all',
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }

    public function mandatory(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_mandatory' => true,
        ]);
    }

    public function optional(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_mandatory' => false,
        ]);
    }
}
