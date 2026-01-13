<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Visa\Models\Visa;
use Modules\Visa\Models\VisaApplication;
use Modules\Visa\Models\VisaCategory;
use Modules\Visa\Models\VisaCountry;
use Modules\Visa\Models\VisaFaq;
use Modules\Visa\Models\VisaFee;
use Modules\Visa\Models\VisaProcessingTime;
use Modules\Visa\Models\VisaRequirement;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('Visa Model', function () {
    it('can be created using factory', function () {
        $visa = Visa::factory()->create();

        expect($visa)->toBeInstanceOf(Visa::class)
            ->and($visa->id)->toBeInt()
            ->and($visa->name)->toBeString()
            ->and($visa->slug)->toBeString();
    });

    it('has required attributes', function () {
        $visa = Visa::factory()->create([
            'name' => 'US Tourist Visa',
            'base_price' => 15000,
            'entry_type' => 'single',
            'validity_days' => 90,
        ]);

        expect($visa->name)->toBe('US Tourist Visa')
            ->and((float) $visa->base_price)->toBe(15000.00)
            ->and($visa->entry_type)->toBe('single')
            ->and($visa->validity_days)->toBe(90);
    });

    it('belongs to a country', function () {
        $country = VisaCountry::factory()->create(['name' => 'United States']);
        $visa = Visa::factory()->create(['country_id' => $country->id]);

        expect($visa->country)->toBeInstanceOf(VisaCountry::class)
            ->and($visa->country->name)->toBe('United States');
    });

    it('belongs to a category', function () {
        $category = VisaCategory::factory()->create(['name' => 'Tourist']);
        $visa = Visa::factory()->create(['category_id' => $category->id]);

        expect($visa->category)->toBeInstanceOf(VisaCategory::class)
            ->and($visa->category->name)->toBe('Tourist');
    });

    it('has many requirements', function () {
        $visa = Visa::factory()->create();
        VisaRequirement::factory()->count(3)->create(['visa_id' => $visa->id]);

        expect($visa->requirements)->toHaveCount(3)
            ->and($visa->requirements->first())->toBeInstanceOf(VisaRequirement::class);
    });

    it('has many fees', function () {
        $visa = Visa::factory()->create();
        VisaFee::factory()->count(2)->create(['visa_id' => $visa->id]);

        expect($visa->fees)->toHaveCount(2)
            ->and($visa->fees->first())->toBeInstanceOf(VisaFee::class);
    });

    it('has many processing times', function () {
        $visa = Visa::factory()->create();
        VisaProcessingTime::factory()->count(2)->create(['visa_id' => $visa->id]);

        expect($visa->processingTimes)->toHaveCount(2)
            ->and($visa->processingTimes->first())->toBeInstanceOf(VisaProcessingTime::class);
    });

    it('has many faqs', function () {
        $visa = Visa::factory()->create();
        VisaFaq::factory()->count(3)->create(['visa_id' => $visa->id]);

        expect($visa->faqs)->toHaveCount(3)
            ->and($visa->faqs->first())->toBeInstanceOf(VisaFaq::class);
    });

    it('has many applications', function () {
        $visa = Visa::factory()->create();
        VisaApplication::factory()->count(2)->create(['visa_id' => $visa->id]);

        expect($visa->applications)->toHaveCount(2);
    });

    it('calculates total price correctly', function () {
        $visa = Visa::factory()->create([
            'base_price' => 10000,
            'embassy_fee' => 5000,
            'service_fee' => 3000,
            'vfs_fee' => 2000,
        ]);

        $expectedTotal = 10000 + 5000 + 3000 + 2000;

        expect($visa->total_price)->toBe((float) $expectedTotal);
    });

    it('generates processing time range string', function () {
        $visa = Visa::factory()->create([
            'processing_days_min' => 5,
            'processing_days_max' => 10,
        ]);

        expect($visa->processing_time_range)->toBe('5-10 days');
    });

    it('can scope active visas', function () {
        Visa::factory()->create(['is_active' => true]);
        Visa::factory()->create(['is_active' => true]);
        Visa::factory()->create(['is_active' => false]);

        expect(Visa::active()->count())->toBe(2);
    });

    it('can scope featured visas', function () {
        Visa::factory()->create(['is_featured' => true, 'is_active' => true]);
        Visa::factory()->create(['is_featured' => false, 'is_active' => true]);

        expect(Visa::where('is_featured', true)->count())->toBe(1);
    });

    it('can scope popular visas', function () {
        Visa::factory()->create(['is_popular' => true, 'is_active' => true]);
        Visa::factory()->create(['is_popular' => true, 'is_active' => true]);
        Visa::factory()->create(['is_popular' => false, 'is_active' => true]);

        expect(Visa::where('is_popular', true)->count())->toBe(2);
    });

    it('is soft deletable', function () {
        $visa = Visa::factory()->create();
        $visaId = $visa->id;

        $visa->delete();

        expect(Visa::find($visaId))->toBeNull()
            ->and(Visa::withTrashed()->find($visaId))->not->toBeNull();
    });
});

describe('VisaCountry Model', function () {
    it('can be created using factory', function () {
        $country = VisaCountry::factory()->create();

        expect($country)->toBeInstanceOf(VisaCountry::class)
            ->and($country->name)->toBeString()
            ->and($country->code)->toBeString();
    });

    it('has many visas', function () {
        $country = VisaCountry::factory()->create();
        Visa::factory()->count(3)->create(['country_id' => $country->id]);

        expect($country->visas)->toHaveCount(3);
    });

    it('can scope active countries', function () {
        VisaCountry::factory()->create(['is_active' => true]);
        VisaCountry::factory()->create(['is_active' => true]);
        VisaCountry::factory()->create(['is_active' => false]);

        expect(VisaCountry::active()->count())->toBe(2);
    });

    it('can scope popular countries', function () {
        VisaCountry::factory()->create(['is_popular' => true]);
        VisaCountry::factory()->create(['is_popular' => false]);

        expect(VisaCountry::where('is_popular', true)->count())->toBe(1);
    });
});

describe('VisaCategory Model', function () {
    it('can be created using factory', function () {
        $category = VisaCategory::factory()->create();

        expect($category)->toBeInstanceOf(VisaCategory::class)
            ->and($category->name)->toBeString();
    });

    it('has many visas', function () {
        $category = VisaCategory::factory()->create();
        Visa::factory()->count(2)->create(['category_id' => $category->id]);

        expect($category->visas)->toHaveCount(2);
    });
});

describe('VisaApplication Model', function () {
    it('can be created using factory', function () {
        $application = VisaApplication::factory()->create();

        expect($application)->toBeInstanceOf(VisaApplication::class)
            ->and($application->application_number)->toBeString();
    });

    it('belongs to a visa', function () {
        $visa = Visa::factory()->create();
        $application = VisaApplication::factory()->create(['visa_id' => $visa->id]);

        expect($application->visa)->toBeInstanceOf(Visa::class);
    });

    it('belongs to a visa country', function () {
        $visaCountry = VisaCountry::factory()->create();
        $application = VisaApplication::factory()->create(['country_id' => $visaCountry->id]);

        expect($application->country_id)->toBe($visaCountry->id);
    });

    it('calculates total travelers correctly', function () {
        $application = VisaApplication::factory()->create([
            'adult_count' => 2,
            'child_count' => 1,
            'infant_count' => 1,
        ]);

        expect($application->total_travelers)->toBe(4);
    });

    it('has correct status values', function () {
        $application = VisaApplication::factory()->create(['status' => 'pending']);

        expect($application->status)->toBe('pending');

        $application->update(['status' => 'processing']);
        expect($application->fresh()->status)->toBe('processing');
    });

    it('has correct payment status values', function () {
        $application = VisaApplication::factory()->create(['payment_status' => 'unpaid']);

        expect($application->payment_status)->toBe('unpaid');

        $application->update(['payment_status' => 'paid']);
        expect($application->fresh()->payment_status)->toBe('paid');
    });

    it('stores due amount correctly', function () {
        $application = VisaApplication::factory()->create([
            'total_amount' => 50000,
            'amount_paid' => 20000,
            'amount_due' => 30000,
        ]);

        expect((float) $application->amount_due)->toBe(30000.00);
    });
});
