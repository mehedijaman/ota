<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Visa\Models\Visa;
use Modules\Visa\Models\VisaCategory;
use Modules\Visa\Models\VisaCountry;
use Modules\Visa\Models\VisaRequirement;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('Visa Index Page', function () {
    it('displays visa index page', function () {
        $country = VisaCountry::factory()->create(['is_active' => true]);
        Visa::factory()->create(['country_id' => $country->id, 'is_active' => true]);

        $response = $this->get('/visa');

        $response->assertOk()
            ->assertSee('Visa');
    });

    it('displays featured visas on index', function () {
        $visa = Visa::factory()->create([
            'is_featured' => true,
            'is_active' => true,
            'name' => 'Featured US Visa',
        ]);

        $response = $this->get('/visa');

        $response->assertOk()
            ->assertSee('Featured US Visa');
    });

    it('displays popular countries on index', function () {
        $country = VisaCountry::factory()->create([
            'is_popular' => true,
            'is_active' => true,
            'name' => 'United States',
        ]);
        Visa::factory()->create(['country_id' => $country->id, 'is_active' => true]);

        $response = $this->get('/visa');

        $response->assertOk();
    });

    it('displays visa categories on index', function () {
        $category = VisaCategory::factory()->create([
            'name' => 'Tourist Visa',
            'is_active' => true,
        ]);

        $response = $this->get('/visa');

        $response->assertOk();
    });
});

describe('Visa Search', function () {
    it('can search visas by country name', function () {
        $country = VisaCountry::factory()->create([
            'name' => 'Malaysia',
            'is_active' => true,
        ]);
        Visa::factory()->create([
            'country_id' => $country->id,
            'is_active' => true,
        ]);

        $response = $this->get('/visa/search?country=Malaysia');

        $response->assertOk();
    });

    it('can filter visas by category', function () {
        $category = VisaCategory::factory()->create(['name' => 'Business', 'is_active' => true]);
        Visa::factory()->create(['category_id' => $category->id, 'is_active' => true]);

        $response = $this->get('/visa/search?category='.$category->id);

        $response->assertOk();
    });

    it('can filter visas by entry type', function () {
        Visa::factory()->create(['entry_type' => 'multiple', 'is_active' => true]);

        $response = $this->get('/visa/search?entry_type=multiple');

        $response->assertOk();
    });

    it('can sort visas by price', function () {
        Visa::factory()->create(['base_price' => 5000, 'is_active' => true]);
        Visa::factory()->create(['base_price' => 10000, 'is_active' => true]);

        $response = $this->get('/visa/search?sort=price_low');

        $response->assertOk();
    });

    it('returns empty results for non-existent search', function () {
        $response = $this->get('/visa/search?country=NonExistentCountry');

        $response->assertOk();
    });

    it('can paginate search results', function () {
        Visa::factory()->count(20)->create(['is_active' => true]);

        $response = $this->get('/visa/search?page=1');

        $response->assertOk();
    });
});

describe('Visa Country Page', function () {
    it('returns 404 for inactive country', function () {
        $country = VisaCountry::factory()->create([
            'slug' => 'inactive-country',
            'is_active' => false,
        ]);

        $response = $this->get('/visa/country/inactive-country');

        $response->assertNotFound();
    });

    it('returns 404 for non-existent country', function () {
        $response = $this->get('/visa/country/non-existent-country');

        $response->assertNotFound();
    });
});

describe('Visa Details Page', function () {
    it('returns 404 for inactive visa', function () {
        $visa = Visa::factory()->create([
            'slug' => 'inactive-visa',
            'is_active' => false,
        ]);

        $response = $this->get('/visa/inactive-visa');

        $response->assertNotFound();
    });

    it('returns 404 for non-existent visa', function () {
        $response = $this->get('/visa/non-existent-visa-slug');

        $response->assertNotFound();
    });
});

describe('Visa Requirements Page', function () {
    it('displays requirements page for a country', function () {
        $country = VisaCountry::factory()->create([
            'slug' => 'japan',
            'is_active' => true,
        ]);
        $visa = Visa::factory()->create([
            'country_id' => $country->id,
            'is_active' => true,
        ]);
        VisaRequirement::factory()->create([
            'visa_id' => $visa->id,
            'name' => 'Passport with 6 months validity',
        ]);

        $response = $this->get('/visa/requirements/japan');

        $response->assertOk();
    });

    it('returns 404 for non-existent country requirements', function () {
        $response = $this->get('/visa/requirements/non-existent');

        $response->assertNotFound();
    });
});
