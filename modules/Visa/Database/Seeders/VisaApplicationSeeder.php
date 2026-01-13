<?php

namespace Modules\Visa\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Visa\Models\Visa;
use Modules\Visa\Models\VisaApplication;
use Modules\Visa\Models\VisaApplicationTraveler;

class VisaApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $visas = Visa::with('country')->take(5)->get();

        if ($visas->isEmpty()) {
            $this->command->warn('Please run VisaSeeder first.');

            return;
        }

        $statuses = [
            'pending',
            'documents_pending',
            'documents_submitted',
            'under_review',
            'processing',
            'submitted_to_embassy',
            'approved',
            'completed',
        ];

        foreach ($visas as $visa) {
            // Create 2 applications per visa (10 total for 5 visas)
            for ($i = 0; $i < 2; $i++) {
                $status = $statuses[array_rand($statuses)];
                $adultCount = rand(1, 3);
                $childCount = rand(0, 2);

                $subtotal = $visa->base_price * ($adultCount + $childCount);
                $embassyFees = $visa->embassy_fee * ($adultCount + $childCount);
                $serviceFees = $visa->service_fee;
                $totalAmount = $subtotal + $embassyFees + $serviceFees + $visa->vfs_fee;

                $isPaid = in_array($status, ['approved', 'completed', 'submitted_to_embassy']);
                $amountPaid = $isPaid ? $totalAmount : ($status === 'processing' ? $totalAmount * 0.5 : 0);

                $application = VisaApplication::create([
                    'application_number' => 'VA-'.strtoupper(Str::random(8)),
                    'visa_id' => $visa->id,
                    'country_id' => $visa->country_id,
                    'status' => $status,
                    'payment_status' => $isPaid ? 'paid' : ($amountPaid > 0 ? 'partial' : 'unpaid'),
                    'adult_count' => $adultCount,
                    'child_count' => $childCount,
                    'infant_count' => 0,
                    'intended_travel_date' => now()->addMonths(rand(1, 4)),
                    'intended_return_date' => now()->addMonths(rand(2, 5)),
                    'travel_purpose' => 'Tourism',
                    'contact_name' => fake()->name(),
                    'contact_email' => fake()->safeEmail(),
                    'contact_phone' => '+880'.fake()->numerify('1#########'),
                    'whatsapp' => '+880'.fake()->numerify('1#########'),
                    'contact_address' => fake()->address(),
                    'city' => fake()->randomElement(['Dhaka', 'Chittagong', 'Sylhet', 'Rajshahi', 'Khulna']),
                    'postal_code' => fake()->numerify('####'),
                    'country' => 'Bangladesh',
                    'emergency_contact_name' => fake()->name(),
                    'emergency_contact_phone' => '+880'.fake()->numerify('1#########'),
                    'emergency_contact_relationship' => fake()->randomElement(['Spouse', 'Parent', 'Sibling']),
                    'subtotal' => $subtotal,
                    'embassy_fees' => $embassyFees,
                    'service_fees' => $serviceFees,
                    'express_fees' => 0,
                    'additional_fees' => 0,
                    'tax_amount' => 0,
                    'discount_amount' => 0,
                    'total_amount' => $totalAmount,
                    'amount_paid' => $amountPaid,
                    'amount_due' => $totalAmount - $amountPaid,
                    'application_source' => fake()->randomElement(['website', 'admin']),
                    'submitted_at' => in_array($status, ['pending', 'draft']) ? null : now()->subDays(rand(5, 30)),
                    'approved_at' => in_array($status, ['approved', 'completed']) ? now()->subDays(rand(1, 5)) : null,
                    'completed_at' => $status === 'completed' ? now() : null,
                ]);

                // Create lead traveler
                $this->createTraveler($application, true, 'adult');

                // Create additional adult travelers
                for ($j = 1; $j < $adultCount; $j++) {
                    $this->createTraveler($application, false, 'adult');
                }

                // Create child travelers
                for ($j = 0; $j < $childCount; $j++) {
                    $this->createTraveler($application, false, 'child');
                }
            }
        }
    }

    protected function createTraveler(VisaApplication $application, bool $isLead, string $type): void
    {
        $gender = fake()->randomElement(['male', 'female']);
        $firstName = $gender === 'male' ? fake()->firstNameMale() : fake()->firstNameFemale();

        $dob = $type === 'adult'
            ? fake()->dateTimeBetween('-60 years', '-18 years')
            : fake()->dateTimeBetween('-17 years', '-2 years');

        VisaApplicationTraveler::create([
            'application_id' => $application->id,
            'type' => $type,
            'is_lead' => $isLead,
            'title' => $type === 'adult' ? ($gender === 'male' ? 'Mr' : fake()->randomElement(['Mrs', 'Ms'])) : null,
            'first_name' => $firstName,
            'last_name' => fake()->lastName(),
            'email' => $isLead ? $application->contact_email : fake()->optional()->safeEmail(),
            'phone' => $isLead ? $application->contact_phone : fake()->optional()->phoneNumber(),
            'date_of_birth' => $dob,
            'gender' => $gender,
            'marital_status' => $type === 'adult' ? fake()->randomElement(['single', 'married']) : null,
            'place_of_birth' => fake()->city(),
            'country_of_birth' => 'Bangladesh',
            'nationality' => 'Bangladeshi',
            'passport_number' => strtoupper(fake()->bothify('??#######')),
            'passport_type' => 'Ordinary',
            'passport_issue_date' => fake()->dateTimeBetween('-5 years', '-1 year'),
            'passport_expiry_date' => fake()->dateTimeBetween('+2 years', '+10 years'),
            'passport_issue_place' => fake()->city(),
            'passport_issue_authority' => 'Department of Immigration and Passports',
            'nid_number' => $type === 'adult' ? fake()->numerify('##########') : null,
            'present_address' => $application->contact_address,
            'present_city' => $application->city,
            'present_postal_code' => $application->postal_code,
            'present_country' => 'Bangladesh',
            'permanent_address' => fake()->address(),
            'permanent_city' => fake()->city(),
            'permanent_postal_code' => fake()->numerify('####'),
            'permanent_country' => 'Bangladesh',
            'employment_status' => $type === 'adult' ? fake()->randomElement(['employed', 'self_employed', 'student']) : 'minor',
            'occupation' => $type === 'adult' ? fake()->jobTitle() : null,
            'employer_name' => $type === 'adult' ? fake()->company() : null,
            'father_name' => fake()->name('male'),
            'mother_name' => fake()->name('female'),
            'has_previous_visa' => fake()->boolean(40),
            'has_visa_refusal' => fake()->boolean(5),
            'has_deportation_history' => false,
            'has_criminal_record' => false,
            'verification_status' => 'pending',
        ]);
    }
}
