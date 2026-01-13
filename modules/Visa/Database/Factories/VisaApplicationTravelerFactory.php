<?php

namespace Modules\Visa\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Visa\Models\VisaApplication;
use Modules\Visa\Models\VisaApplicationTraveler;

class VisaApplicationTravelerFactory extends Factory
{
    protected $model = VisaApplicationTraveler::class;

    public function definition(): array
    {
        $gender = fake()->randomElement(['male', 'female']);
        $firstName = $gender === 'male' ? fake()->firstNameMale() : fake()->firstNameFemale();

        return [
            'application_id' => VisaApplication::factory(),
            'type' => 'adult',
            'is_lead' => false,
            'title' => $gender === 'male' ? 'Mr' : fake()->randomElement(['Mrs', 'Ms']),
            'first_name' => $firstName,
            'last_name' => fake()->lastName(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'date_of_birth' => fake()->dateTimeBetween('-60 years', '-18 years'),
            'gender' => $gender,
            'marital_status' => fake()->randomElement(['single', 'married', 'divorced', 'widowed']),
            'place_of_birth' => fake()->city(),
            'country_of_birth' => 'Bangladesh',
            'nationality' => 'Bangladeshi',
            'passport_number' => strtoupper(fake()->bothify('??#######')),
            'passport_type' => 'Ordinary',
            'passport_issue_date' => fake()->dateTimeBetween('-5 years', '-1 year'),
            'passport_expiry_date' => fake()->dateTimeBetween('+1 year', '+10 years'),
            'passport_issue_place' => fake()->city(),
            'passport_issue_authority' => 'Department of Immigration and Passports',
            'nid_number' => fake()->numerify('##########'),
            'present_address' => fake()->address(),
            'present_city' => fake()->city(),
            'present_postal_code' => fake()->postcode(),
            'present_country' => 'Bangladesh',
            'permanent_address' => fake()->address(),
            'permanent_city' => fake()->city(),
            'permanent_postal_code' => fake()->postcode(),
            'permanent_country' => 'Bangladesh',
            'employment_status' => fake()->randomElement(['employed', 'self_employed', 'student', 'retired']),
            'occupation' => fake()->jobTitle(),
            'employer_name' => fake()->company(),
            'employer_address' => fake()->address(),
            'employer_phone' => fake()->phoneNumber(),
            'job_title' => fake()->jobTitle(),
            'monthly_income' => fake()->randomElement([30000, 50000, 75000, 100000, 150000]),
            'father_name' => fake()->name('male'),
            'mother_name' => fake()->name('female'),
            'has_previous_visa' => fake()->boolean(40),
            'has_visa_refusal' => fake()->boolean(10),
            'has_deportation_history' => false,
            'has_criminal_record' => false,
            'verification_status' => 'pending',
        ];
    }

    public function lead(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_lead' => true,
        ]);
    }

    public function child(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'child',
            'title' => null,
            'date_of_birth' => fake()->dateTimeBetween('-17 years', '-2 years'),
            'employment_status' => 'minor',
            'occupation' => null,
            'employer_name' => null,
            'monthly_income' => null,
        ]);
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => 'verified',
            'verified_at' => now(),
        ]);
    }
}
