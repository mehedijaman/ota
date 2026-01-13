<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Visa\Models\Visa;
use Modules\Visa\Models\VisaApplication;
use Modules\Visa\Models\VisaCountry;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('Visa Booking Page', function () {
    it('requires authentication for apply page', function () {
        $country = VisaCountry::factory()->active()->create();
        $visa = Visa::factory()->create([
            'is_active' => true,
            'country_id' => $country->id,
        ]);

        $response = $this->get('/visa/'.$visa->slug.'/apply');

        $response->assertRedirect('/login');
    });

    it('returns 404 for inactive visa application', function () {
        $user = User::factory()->create();
        $country = VisaCountry::factory()->active()->create();
        $visa = Visa::factory()->create([
            'is_active' => false,
            'country_id' => $country->id,
        ]);

        $response = $this->actingAs($user)
            ->get('/visa/'.$visa->slug.'/apply');

        $response->assertNotFound();
    });

    it('returns 404 for non-existent visa application', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/visa/non-existent-visa/apply');

        $response->assertNotFound();
    });
});

describe('Visa Booking Submission', function () {
    it('validates required booking fields', function () {
        $user = User::factory()->create();
        $country = VisaCountry::factory()->active()->create();
        $visa = Visa::factory()->create([
            'is_active' => true,
            'country_id' => $country->id,
        ]);

        $response = $this->actingAs($user)
            ->post('/visa/'.$visa->slug.'/apply', []);

        $response->assertSessionHasErrors(['intended_travel_date', 'adult_count', 'contact_name', 'contact_email', 'contact_phone', 'travelers']);
    });

    it('creates booking with valid data', function () {
        $user = User::factory()->create();
        $country = VisaCountry::factory()->active()->create();
        $visa = Visa::factory()->create([
            'is_active' => true,
            'country_id' => $country->id,
            'base_price' => 10000,
            'embassy_fee' => 5000,
            'service_fee' => 3000,
        ]);

        $response = $this->actingAs($user)
            ->post('/visa/'.$visa->slug.'/apply', [
                'intended_travel_date' => now()->addMonth()->format('Y-m-d'),
                'adult_count' => 1,
                'child_count' => 0,
                'contact_name' => 'John Doe',
                'contact_email' => 'john@example.com',
                'contact_phone' => '+8801712345678',
                'payment_method' => 'later',
                'travelers' => [
                    [
                        'type' => 'adult',
                        'is_lead' => true,
                        'first_name' => 'John',
                        'last_name' => 'Doe',
                        'passport_number' => 'AB123456',
                        'passport_issue_date' => now()->subYear()->format('Y-m-d'),
                        'passport_expiry_date' => now()->addYears(2)->format('Y-m-d'),
                        'date_of_birth' => '1990-01-15',
                        'nationality' => 'Bangladeshi',
                        'gender' => 'male',
                    ],
                ],
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('visa_applications', [
            'visa_id' => $visa->id,
            'created_by' => $user->id,
            'contact_name' => 'John Doe',
            'contact_email' => 'john@example.com',
        ]);
    });

    it('creates travelers for the application', function () {
        $user = User::factory()->create();
        $country = VisaCountry::factory()->active()->create();
        $visa = Visa::factory()->create([
            'is_active' => true,
            'country_id' => $country->id,
        ]);

        $this->actingAs($user)
            ->post('/visa/'.$visa->slug.'/apply', [
                'intended_travel_date' => now()->addMonth()->format('Y-m-d'),
                'adult_count' => 1,
                'child_count' => 0,
                'contact_name' => 'John Doe',
                'contact_email' => 'john@example.com',
                'contact_phone' => '+8801712345678',
                'payment_method' => 'later',
                'travelers' => [
                    [
                        'type' => 'adult',
                        'is_lead' => true,
                        'first_name' => 'John',
                        'last_name' => 'Doe',
                        'passport_number' => 'AB123456',
                        'passport_issue_date' => now()->subYear()->format('Y-m-d'),
                        'passport_expiry_date' => now()->addYears(2)->format('Y-m-d'),
                        'date_of_birth' => '1990-01-15',
                        'nationality' => 'Bangladeshi',
                        'gender' => 'male',
                    ],
                ],
            ]);

        $application = VisaApplication::where('created_by', $user->id)->first();

        expect($application)->not->toBeNull()
            ->and($application->travelers)->toHaveCount(1)
            ->and($application->travelers->first()->first_name)->toBe('John');
    });

    it('calculates correct total amount', function () {
        $user = User::factory()->create();
        $country = VisaCountry::factory()->active()->create();
        $visa = Visa::factory()->create([
            'is_active' => true,
            'country_id' => $country->id,
            'base_price' => 10000,
            'embassy_fee' => 5000,
            'service_fee' => 3000,
            'vfs_fee' => 2000,
        ]);

        $this->actingAs($user)
            ->post('/visa/'.$visa->slug.'/apply', [
                'intended_travel_date' => now()->addMonth()->format('Y-m-d'),
                'adult_count' => 2,
                'child_count' => 0,
                'contact_name' => 'John Doe',
                'contact_email' => 'john@example.com',
                'contact_phone' => '+8801712345678',
                'payment_method' => 'later',
                'travelers' => [
                    [
                        'type' => 'adult',
                        'is_lead' => true,
                        'first_name' => 'John',
                        'last_name' => 'Doe',
                        'passport_number' => 'AB123456',
                        'passport_issue_date' => now()->subYear()->format('Y-m-d'),
                        'passport_expiry_date' => now()->addYears(2)->format('Y-m-d'),
                        'date_of_birth' => '1990-01-15',
                        'nationality' => 'Bangladeshi',
                        'gender' => 'male',
                    ],
                    [
                        'type' => 'adult',
                        'is_lead' => false,
                        'first_name' => 'Jane',
                        'last_name' => 'Doe',
                        'passport_number' => 'CD789012',
                        'passport_issue_date' => now()->subYear()->format('Y-m-d'),
                        'passport_expiry_date' => now()->addYears(2)->format('Y-m-d'),
                        'date_of_birth' => '1992-05-20',
                        'nationality' => 'Bangladeshi',
                        'gender' => 'female',
                    ],
                ],
            ]);

        $application = VisaApplication::where('created_by', $user->id)->first();

        // base_price * 2 = 20000, embassy_fee * 2 = 10000, service_fee = 3000, vfs_fee = 2000
        // Total = 20000 + 10000 + 3000 + 2000 = 35000
        expect((float) $application->total_amount)->toBe(35000.00);
    });

    it('validates traveler passport expiry must be in future', function () {
        $user = User::factory()->create();
        $country = VisaCountry::factory()->active()->create();
        $visa = Visa::factory()->create([
            'is_active' => true,
            'country_id' => $country->id,
        ]);

        $response = $this->actingAs($user)
            ->post('/visa/'.$visa->slug.'/apply', [
                'intended_travel_date' => now()->addMonth()->format('Y-m-d'),
                'adult_count' => 1,
                'child_count' => 0,
                'contact_name' => 'John Doe',
                'contact_email' => 'john@example.com',
                'contact_phone' => '+8801712345678',
                'payment_method' => 'later',
                'travelers' => [
                    [
                        'type' => 'adult',
                        'is_lead' => true,
                        'first_name' => 'John',
                        'last_name' => 'Doe',
                        'passport_number' => 'AB123456',
                        'passport_issue_date' => now()->subYears(5)->format('Y-m-d'),
                        'passport_expiry_date' => now()->subMonth()->format('Y-m-d'),
                        'date_of_birth' => '1990-01-15',
                        'nationality' => 'Bangladeshi',
                        'gender' => 'male',
                    ],
                ],
            ]);

        $response->assertSessionHasErrors('travelers.0.passport_expiry_date');
    });

    it('validates travel date must be in future', function () {
        $user = User::factory()->create();
        $country = VisaCountry::factory()->active()->create();
        $visa = Visa::factory()->create([
            'is_active' => true,
            'country_id' => $country->id,
        ]);

        $response = $this->actingAs($user)
            ->post('/visa/'.$visa->slug.'/apply', [
                'intended_travel_date' => now()->subMonth()->format('Y-m-d'),
                'adult_count' => 1,
                'contact_name' => 'John Doe',
                'contact_email' => 'john@example.com',
                'contact_phone' => '+8801712345678',
                'payment_method' => 'later',
                'travelers' => [
                    [
                        'type' => 'adult',
                        'is_lead' => true,
                        'first_name' => 'John',
                        'last_name' => 'Doe',
                        'passport_number' => 'AB123456',
                        'passport_issue_date' => now()->subYear()->format('Y-m-d'),
                        'passport_expiry_date' => now()->addYears(2)->format('Y-m-d'),
                        'date_of_birth' => '1990-01-15',
                        'nationality' => 'Bangladeshi',
                        'gender' => 'male',
                    ],
                ],
            ]);

        $response->assertSessionHasErrors('intended_travel_date');
    });
});

describe('Visa Payment', function () {
    it('requires authentication for payment page', function () {
        $country = VisaCountry::factory()->active()->create();
        $visa = Visa::factory()->create([
            'is_active' => true,
            'country_id' => $country->id,
        ]);
        $application = VisaApplication::factory()->create([
            'visa_id' => $visa->id,
            'country_id' => $country->id,
            'payment_status' => 'unpaid',
        ]);

        $response = $this->get('/visa/application/'.$application->application_number.'/payment');

        $response->assertRedirect('/login');
    });

    it('shows payment page for unpaid application', function () {
        $user = User::factory()->create();
        $country = VisaCountry::factory()->active()->create();
        $visa = Visa::factory()->create([
            'is_active' => true,
            'country_id' => $country->id,
        ]);
        $application = VisaApplication::factory()->create([
            'visa_id' => $visa->id,
            'country_id' => $country->id,
            'created_by' => $user->id,
            'payment_status' => 'unpaid',
        ]);

        $response = $this->actingAs($user)
            ->get('/visa/application/'.$application->application_number.'/payment');

        $response->assertOk();
    });
});

describe('Visa Confirmation', function () {
    it('requires authentication for confirmation page', function () {
        $country = VisaCountry::factory()->active()->create();
        $visa = Visa::factory()->create([
            'is_active' => true,
            'country_id' => $country->id,
        ]);
        $application = VisaApplication::factory()->create([
            'visa_id' => $visa->id,
            'country_id' => $country->id,
        ]);

        $response = $this->get('/visa/'.$visa->slug.'/confirmation/'.$application->application_number);

        $response->assertRedirect('/login');
    });

    it('shows confirmation page for user application', function () {
        $user = User::factory()->create();
        $country = VisaCountry::factory()->active()->create();
        $visa = Visa::factory()->create([
            'is_active' => true,
            'country_id' => $country->id,
        ]);
        $application = VisaApplication::factory()->create([
            'visa_id' => $visa->id,
            'country_id' => $country->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->get('/visa/'.$visa->slug.'/confirmation/'.$application->application_number);

        $response->assertOk();
    });
});
