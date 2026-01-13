<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Visa\Models\Visa;
use Modules\Visa\Models\VisaApplication;
use Modules\Visa\Models\VisaCategory;
use Modules\Visa\Models\VisaCountry;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('Admin Visa Routes Access', function () {
    it('does not allow public access to visa countries route', function () {
        $response = $this->get('/visa-countries');

        // Route may return 302 (redirect), 401 (unauthorized), 403 (forbidden), or 404 (route not found for guests)
        expect($response->isSuccessful())->toBeFalse();
    });

    it('does not allow public access to visa categories route', function () {
        $response = $this->get('/visa-categories');

        expect($response->isSuccessful())->toBeFalse();
    });

    it('does not allow public access to visas route', function () {
        $response = $this->get('/visas');

        expect($response->isSuccessful())->toBeFalse();
    });

    it('does not allow public access to visa applications route', function () {
        $response = $this->get('/visa-applications');

        expect($response->isSuccessful())->toBeFalse();
    });
});

describe('Visa Admin Models', function () {
    it('can create visa country via factory', function () {
        $country = VisaCountry::factory()->create();

        expect($country)->toBeInstanceOf(VisaCountry::class)
            ->and($country->id)->toBeGreaterThan(0)
            ->and($country->name)->not->toBeEmpty();
    });

    it('can create visa category via factory', function () {
        $category = VisaCategory::factory()->create();

        expect($category)->toBeInstanceOf(VisaCategory::class)
            ->and($category->id)->toBeGreaterThan(0)
            ->and($category->name)->not->toBeEmpty();
    });

    it('can create visa via factory', function () {
        $country = VisaCountry::factory()->create();
        $visa = Visa::factory()->create(['country_id' => $country->id]);

        expect($visa)->toBeInstanceOf(Visa::class)
            ->and($visa->id)->toBeGreaterThan(0)
            ->and($visa->name)->not->toBeEmpty();
    });

    it('can create visa application via factory', function () {
        $country = VisaCountry::factory()->create();
        $visa = Visa::factory()->create(['country_id' => $country->id]);
        $application = VisaApplication::factory()->create([
            'visa_id' => $visa->id,
            'country_id' => $country->id,
        ]);

        expect($application)->toBeInstanceOf(VisaApplication::class)
            ->and($application->id)->toBeGreaterThan(0)
            ->and($application->application_number)->not->toBeEmpty();
    });
});

describe('Visa Country CRUD Operations', function () {
    it('stores a new visa country in database', function () {
        $countryData = [
            'name' => 'Test Country',
            'slug' => 'test-country',
            'code' => 'TC',
            'continent' => 'Asia',
            'is_active' => true,
        ];

        $country = VisaCountry::create($countryData);

        $this->assertDatabaseHas('visa_countries', [
            'name' => 'Test Country',
            'code' => 'TC',
        ]);

        expect($country->name)->toBe('Test Country')
            ->and($country->code)->toBe('TC');
    });

    it('updates a visa country in database', function () {
        $country = VisaCountry::factory()->create();

        $country->update(['name' => 'Updated Country Name']);

        $this->assertDatabaseHas('visa_countries', [
            'id' => $country->id,
            'name' => 'Updated Country Name',
        ]);
    });

    it('soft deletes a visa country', function () {
        $country = VisaCountry::factory()->create();
        $countryId = $country->id;

        $country->delete();

        $this->assertSoftDeleted('visa_countries', ['id' => $countryId]);
    });
});

describe('Visa Category CRUD Operations', function () {
    it('stores a new visa category in database', function () {
        $categoryData = [
            'name' => 'Test Category',
            'slug' => 'test-category',
            'code' => 'TCAT',
            'is_active' => true,
        ];

        $category = VisaCategory::create($categoryData);

        $this->assertDatabaseHas('visa_categories', [
            'name' => 'Test Category',
        ]);
    });

    it('updates a visa category in database', function () {
        $category = VisaCategory::factory()->create();

        $category->update(['name' => 'Updated Category Name']);

        $this->assertDatabaseHas('visa_categories', [
            'id' => $category->id,
            'name' => 'Updated Category Name',
        ]);
    });

    it('soft deletes a visa category', function () {
        $category = VisaCategory::factory()->create();
        $categoryId = $category->id;

        $category->delete();

        $this->assertSoftDeleted('visa_categories', ['id' => $categoryId]);
    });
});

describe('Visa CRUD Operations', function () {
    it('stores a new visa in database', function () {
        $country = VisaCountry::factory()->create();

        $visaData = [
            'name' => 'Test Visa',
            'slug' => 'test-visa',
            'country_id' => $country->id,
            'visa_type' => 'tourist',
            'entry_type' => 'single',
            'base_price' => 10000,
            'is_active' => true,
        ];

        $visa = Visa::create($visaData);

        $this->assertDatabaseHas('visas', [
            'name' => 'Test Visa',
            'country_id' => $country->id,
        ]);
    });

    it('updates a visa in database', function () {
        $country = VisaCountry::factory()->create();
        $visa = Visa::factory()->create(['country_id' => $country->id]);

        $visa->update(['name' => 'Updated Visa Name']);

        $this->assertDatabaseHas('visas', [
            'id' => $visa->id,
            'name' => 'Updated Visa Name',
        ]);
    });

    it('soft deletes a visa', function () {
        $country = VisaCountry::factory()->create();
        $visa = Visa::factory()->create(['country_id' => $country->id]);
        $visaId = $visa->id;

        $visa->delete();

        $this->assertSoftDeleted('visas', ['id' => $visaId]);
    });
});

describe('Visa Application Operations', function () {
    it('stores a new visa application in database', function () {
        $country = VisaCountry::factory()->create();
        $visa = Visa::factory()->create(['country_id' => $country->id]);

        $applicationData = [
            'application_number' => 'VA-TEST123',
            'visa_id' => $visa->id,
            'country_id' => $country->id,
            'contact_name' => 'Test Contact',
            'contact_email' => 'test@example.com',
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'adult_count' => 1,
        ];

        $application = VisaApplication::create($applicationData);

        $this->assertDatabaseHas('visa_applications', [
            'application_number' => 'VA-TEST123',
            'contact_name' => 'Test Contact',
        ]);
    });

    it('updates visa application status', function () {
        $country = VisaCountry::factory()->create();
        $visa = Visa::factory()->create(['country_id' => $country->id]);
        $application = VisaApplication::factory()->create([
            'visa_id' => $visa->id,
            'country_id' => $country->id,
            'status' => 'pending',
        ]);

        $application->update(['status' => 'processing']);

        $this->assertDatabaseHas('visa_applications', [
            'id' => $application->id,
            'status' => 'processing',
        ]);
    });

    it('updates visa application payment status', function () {
        $country = VisaCountry::factory()->create();
        $visa = Visa::factory()->create(['country_id' => $country->id]);
        $application = VisaApplication::factory()->create([
            'visa_id' => $visa->id,
            'country_id' => $country->id,
            'payment_status' => 'unpaid',
        ]);

        $application->update(['payment_status' => 'paid']);

        $this->assertDatabaseHas('visa_applications', [
            'id' => $application->id,
            'payment_status' => 'paid',
        ]);
    });

    it('soft deletes a visa application', function () {
        $country = VisaCountry::factory()->create();
        $visa = Visa::factory()->create(['country_id' => $country->id]);
        $application = VisaApplication::factory()->create([
            'visa_id' => $visa->id,
            'country_id' => $country->id,
        ]);
        $applicationId = $application->id;

        $application->delete();

        $this->assertSoftDeleted('visa_applications', ['id' => $applicationId]);
    });
});
