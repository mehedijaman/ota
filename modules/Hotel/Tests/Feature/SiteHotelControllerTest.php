<?php

namespace Modules\Hotel\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Hotel\Models\Hotel;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('can view hotel search page', function () {
    $response = $this->get(route('site.hotel.index'));
    $response->assertOk();
});

it('can search for hotels', function () {
    $hotel = Hotel::factory()->create(['name' => 'Seaview Resort', 'is_active' => true]);
    Hotel::factory()->create(['name' => 'Mountain Lodge', 'is_active' => true]);

    // Note: Search relies on query parameters like location, check_in, etc.
    // 'location' usually maps to city/country or hotel name in search service.
    $response = $this->get(route('site.hotel.search', ['location' => 'Seaview']));

    $response->assertOk();
    $response->assertSee('Seaview Resort');
});

it('can view hotel details', function () {
    $hotel = Hotel::factory()->create(['is_active' => true]);

    $response = $this->get(route('site.hotel.show', $hotel->slug));

    $response->assertOk();
    $response->assertSee($hotel->name);
});

it('returns 404 for inactive hotel', function () {
    $hotel = Hotel::factory()->create(['is_active' => false]);

    $response = $this->get(route('site.hotel.show', $hotel->slug));

    // assertNotFound checks for 404 status
    $response->assertNotFound();
});
