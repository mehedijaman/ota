<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Visa\Models\Visa;
use Modules\Visa\Models\VisaCategory;
use Modules\Visa\Models\VisaCountry;
use Modules\Visa\Services\SiteVisaService;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('SiteVisaService::getFeaturedVisas', function () {
    it('returns only featured visas', function () {
        Visa::factory()->create(['is_featured' => true, 'is_active' => true]);
        Visa::factory()->create(['is_featured' => true, 'is_active' => true]);
        Visa::factory()->create(['is_featured' => false, 'is_active' => true]);

        $visas = SiteVisaService::getFeaturedVisas();

        expect($visas)->toHaveCount(2);
    });

    it('returns only active featured visas', function () {
        Visa::factory()->create(['is_featured' => true, 'is_active' => true]);
        Visa::factory()->create(['is_featured' => true, 'is_active' => false]);

        $visas = SiteVisaService::getFeaturedVisas();

        expect($visas)->toHaveCount(1);
    });

    it('respects limit parameter', function () {
        Visa::factory()->count(10)->create(['is_featured' => true, 'is_active' => true]);

        $visas = SiteVisaService::getFeaturedVisas(5);

        expect($visas)->toHaveCount(5);
    });

    it('eager loads country and category', function () {
        $visa = Visa::factory()->create(['is_featured' => true, 'is_active' => true]);

        $visas = SiteVisaService::getFeaturedVisas();

        expect($visas->first()->relationLoaded('country'))->toBeTrue()
            ->and($visas->first()->relationLoaded('category'))->toBeTrue();
    });
});

describe('SiteVisaService::getPopularVisas', function () {
    it('returns only popular visas', function () {
        Visa::factory()->create(['is_popular' => true, 'is_active' => true]);
        Visa::factory()->create(['is_popular' => false, 'is_active' => true]);

        $visas = SiteVisaService::getPopularVisas();

        expect($visas)->toHaveCount(1);
    });

    it('respects limit parameter', function () {
        Visa::factory()->count(10)->create(['is_popular' => true, 'is_active' => true]);

        $visas = SiteVisaService::getPopularVisas(3);

        expect($visas)->toHaveCount(3);
    });
});

describe('SiteVisaService::getPopularCountries', function () {
    it('returns only popular countries', function () {
        VisaCountry::factory()->create(['is_popular' => true, 'is_active' => true]);
        VisaCountry::factory()->create(['is_popular' => true, 'is_active' => true]);
        VisaCountry::factory()->create(['is_popular' => false, 'is_active' => true]);

        $countries = SiteVisaService::getPopularCountries();

        expect($countries)->toHaveCount(2);
    });

    it('returns only active popular countries', function () {
        VisaCountry::factory()->create(['is_popular' => true, 'is_active' => true]);
        VisaCountry::factory()->create(['is_popular' => true, 'is_active' => false]);

        $countries = SiteVisaService::getPopularCountries();

        expect($countries)->toHaveCount(1);
    });

    it('respects limit parameter', function () {
        VisaCountry::factory()->count(15)->create(['is_popular' => true, 'is_active' => true]);

        $countries = SiteVisaService::getPopularCountries(5);

        expect($countries)->toHaveCount(5);
    });

    it('includes visa count', function () {
        $country = VisaCountry::factory()->create(['is_popular' => true, 'is_active' => true]);
        Visa::factory()->count(3)->create(['country_id' => $country->id, 'is_active' => true]);

        $countries = SiteVisaService::getPopularCountries();

        expect($countries->first()->visas_count)->toBe(3);
    });
});

describe('SiteVisaService::getActiveCountries', function () {
    it('returns only active countries', function () {
        VisaCountry::factory()->create(['is_active' => true]);
        VisaCountry::factory()->create(['is_active' => true]);
        VisaCountry::factory()->create(['is_active' => false]);

        $countries = SiteVisaService::getActiveCountries();

        expect($countries)->toHaveCount(2);
    });

    it('orders by sort_order and name', function () {
        VisaCountry::factory()->create(['name' => 'Zambia', 'sort_order' => 1, 'is_active' => true]);
        VisaCountry::factory()->create(['name' => 'Australia', 'sort_order' => 1, 'is_active' => true]);
        VisaCountry::factory()->create(['name' => 'Japan', 'sort_order' => 0, 'is_active' => true]);

        $countries = SiteVisaService::getActiveCountries();

        expect($countries->first()->name)->toBe('Japan');
    });
});

describe('SiteVisaService::getActiveCategories', function () {
    it('returns only active categories', function () {
        VisaCategory::factory()->create(['is_active' => true]);
        VisaCategory::factory()->create(['is_active' => false]);

        $categories = SiteVisaService::getActiveCategories();

        expect($categories)->toHaveCount(1);
    });

    it('includes visa count', function () {
        $category = VisaCategory::factory()->create(['is_active' => true]);
        Visa::factory()->count(4)->create(['category_id' => $category->id, 'is_active' => true]);

        $categories = SiteVisaService::getActiveCategories();

        expect($categories->first()->visas_count)->toBe(4);
    });
});

describe('SiteVisaService::searchVisas', function () {
    it('returns paginated results', function () {
        Visa::factory()->count(15)->create(['is_active' => true]);

        $visas = SiteVisaService::searchVisas();

        expect($visas)->toBeInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class)
            ->and($visas->count())->toBe(12);
    });

    it('filters by country when provided', function () {
        $country1 = VisaCountry::factory()->create(['is_active' => true]);
        $country2 = VisaCountry::factory()->create(['is_active' => true]);

        Visa::factory()->count(3)->create(['country_id' => $country1->id, 'is_active' => true]);
        Visa::factory()->count(2)->create(['country_id' => $country2->id, 'is_active' => true]);

        request()->merge(['country' => $country1->id]);

        $visas = SiteVisaService::searchVisas();

        expect($visas->total())->toBe(3);
    });

    it('filters by category when provided', function () {
        $category1 = VisaCategory::factory()->create(['is_active' => true]);
        $category2 = VisaCategory::factory()->create(['is_active' => true]);

        Visa::factory()->count(2)->create(['category_id' => $category1->id, 'is_active' => true]);
        Visa::factory()->count(3)->create(['category_id' => $category2->id, 'is_active' => true]);

        request()->merge(['category' => $category2->id]);

        $visas = SiteVisaService::searchVisas();

        expect($visas->total())->toBe(3);
    });

    it('filters by entry type when provided', function () {
        Visa::factory()->create(['entry_type' => 'single', 'is_active' => true]);
        Visa::factory()->create(['entry_type' => 'multiple', 'is_active' => true]);
        Visa::factory()->create(['entry_type' => 'multiple', 'is_active' => true]);

        request()->merge(['entry_type' => 'multiple']);

        $visas = SiteVisaService::searchVisas();

        expect($visas->total())->toBe(2);
    });

    it('filters by processing time when provided', function () {
        Visa::factory()->create(['processing_days_max' => 5, 'is_active' => true]);
        Visa::factory()->create(['processing_days_max' => 10, 'is_active' => true]);
        Visa::factory()->create(['processing_days_max' => 20, 'is_active' => true]);

        request()->merge(['processing' => '10']);

        $visas = SiteVisaService::searchVisas();

        expect($visas->total())->toBe(2);
    });

    it('searches by name when q provided', function () {
        Visa::factory()->create(['name' => 'US Tourist Visa', 'is_active' => true]);
        Visa::factory()->create(['name' => 'UK Work Visa', 'is_active' => true]);
        Visa::factory()->create(['name' => 'Japan Transit Visa', 'is_active' => true]);

        request()->merge(['q' => 'Tourist']);

        $visas = SiteVisaService::searchVisas();

        expect($visas->total())->toBe(1);
    });

    it('sorts by price low to high', function () {
        Visa::factory()->create(['base_price' => 20000, 'service_fee' => 0, 'embassy_fee' => 0, 'is_active' => true]);
        Visa::factory()->create(['base_price' => 10000, 'service_fee' => 0, 'embassy_fee' => 0, 'is_active' => true]);

        request()->merge(['sort' => 'price_low']);

        $visas = SiteVisaService::searchVisas();

        expect((float) $visas->first()->base_price)->toBe(10000.00);
    });

    it('sorts by price high to low', function () {
        Visa::factory()->create(['base_price' => 10000, 'service_fee' => 0, 'embassy_fee' => 0, 'is_active' => true]);
        Visa::factory()->create(['base_price' => 20000, 'service_fee' => 0, 'embassy_fee' => 0, 'is_active' => true]);

        request()->merge(['sort' => 'price_high']);

        $visas = SiteVisaService::searchVisas();

        expect((float) $visas->first()->base_price)->toBe(20000.00);
    });

    it('sorts by fastest processing', function () {
        Visa::factory()->create(['processing_days_min' => 10, 'is_active' => true]);
        Visa::factory()->create(['processing_days_min' => 3, 'is_active' => true]);

        request()->merge(['sort' => 'fastest']);

        $visas = SiteVisaService::searchVisas();

        expect($visas->first()->processing_days_min)->toBe(3);
    });
});

describe('SiteVisaService::getVisaBySlug', function () {
    it('returns visa by slug', function () {
        $visa = Visa::factory()->create(['slug' => 'test-visa', 'is_active' => true]);

        $result = SiteVisaService::getVisaBySlug('test-visa');

        expect($result)->toBeInstanceOf(Visa::class)
            ->and($result->id)->toBe($visa->id);
    });

    it('returns null for inactive visa', function () {
        Visa::factory()->create(['slug' => 'inactive-visa', 'is_active' => false]);

        $result = SiteVisaService::getVisaBySlug('inactive-visa');

        expect($result)->toBeNull();
    });

    it('returns null for non-existent slug', function () {
        $result = SiteVisaService::getVisaBySlug('non-existent');

        expect($result)->toBeNull();
    });

    it('eager loads all relationships', function () {
        $visa = Visa::factory()->create(['slug' => 'loaded-visa', 'is_active' => true]);

        $result = SiteVisaService::getVisaBySlug('loaded-visa');

        expect($result->relationLoaded('country'))->toBeTrue()
            ->and($result->relationLoaded('category'))->toBeTrue()
            ->and($result->relationLoaded('requirements'))->toBeTrue()
            ->and($result->relationLoaded('fees'))->toBeTrue()
            ->and($result->relationLoaded('faqs'))->toBeTrue();
    });
});

describe('SiteVisaService::getVisasByCountry', function () {
    it('returns all visas for a country', function () {
        $country = VisaCountry::factory()->create(['is_active' => true]);
        Visa::factory()->count(3)->create(['country_id' => $country->id, 'is_active' => true]);

        $visas = SiteVisaService::getVisasByCountry($country);

        expect($visas)->toHaveCount(3);
    });

    it('returns only active visas for country', function () {
        $country = VisaCountry::factory()->create(['is_active' => true]);
        Visa::factory()->create(['country_id' => $country->id, 'is_active' => true]);
        Visa::factory()->create(['country_id' => $country->id, 'is_active' => false]);

        $visas = SiteVisaService::getVisasByCountry($country);

        expect($visas)->toHaveCount(1);
    });
});

describe('SiteVisaService::getRelatedVisas', function () {
    it('returns related visas from same country', function () {
        $country = VisaCountry::factory()->create();
        $visa1 = Visa::factory()->create(['country_id' => $country->id, 'is_active' => true]);
        $visa2 = Visa::factory()->create(['country_id' => $country->id, 'is_active' => true]);

        $related = SiteVisaService::getRelatedVisas($visa1);

        expect($related)->toHaveCount(1)
            ->and($related->first()->id)->toBe($visa2->id);
    });

    it('returns related visas from same category', function () {
        $category = VisaCategory::factory()->create();
        $visa1 = Visa::factory()->create(['category_id' => $category->id, 'is_active' => true]);
        $visa2 = Visa::factory()->create(['category_id' => $category->id, 'is_active' => true]);

        $related = SiteVisaService::getRelatedVisas($visa1);

        expect($related)->toHaveCount(1)
            ->and($related->first()->id)->toBe($visa2->id);
    });

    it('excludes the current visa', function () {
        $country = VisaCountry::factory()->create();
        $visa = Visa::factory()->create(['country_id' => $country->id, 'is_active' => true]);

        $related = SiteVisaService::getRelatedVisas($visa);

        expect($related)->toHaveCount(0);
    });

    it('respects limit parameter', function () {
        $country = VisaCountry::factory()->create();
        $visa1 = Visa::factory()->create(['country_id' => $country->id, 'is_active' => true]);
        Visa::factory()->count(5)->create(['country_id' => $country->id, 'is_active' => true]);

        $related = SiteVisaService::getRelatedVisas($visa1, 2);

        expect($related)->toHaveCount(2);
    });
});
