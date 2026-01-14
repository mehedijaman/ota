<?php

namespace Modules\Hotel\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelCity;
use Modules\Hotel\Models\HotelInventory;
use Modules\Hotel\Models\HotelRate;
use Modules\Hotel\Models\HotelRatePlan;
use Modules\Hotel\Models\HotelRoom;
use Modules\Hotel\Services\HotelSearchService;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->service = new HotelSearchService;
});

it('filters hotels by location', function () {
    $city = HotelCity::factory()->create(['name' => 'Random City '.rand(1000, 9999)]);
    $hotel1 = Hotel::factory()->create(['name' => 'Ocean Paradise', 'city_id' => $city->id, 'is_active' => true]);
    $hotel2 = Hotel::factory()->create(['name' => 'Mountain View', 'is_active' => true]);

    // Search by City Name (via destination params usually mapped to city name search in service)
    // The service uses 'destination' to search hotel name OR city name.
    $results = $this->service->search(['destination' => 'Ocean']);

    expect($results->pluck('id'))->toContain($hotel1->id)
        ->not->toContain($hotel2->id);
});

// ...

it('checks availability correctly', function () {
    $hotel = Hotel::factory()->create(['is_active' => true]);
    $room = HotelRoom::factory()->create(['hotel_id' => $hotel->id, 'max_adults' => 2, 'is_active' => true]);

    // Seed inventory and rates for today using insert to bypass model casting timestamp issues in SQLite
    $date = now()->format('Y-m-d');

    HotelInventory::insert([

        'room_id' => $room->id,
        'date' => $date,
        'total_inventory' => 5,
        'available' => 5,
        'booked' => 0,
        'blocked' => 0,
        'held' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $ratePlan = HotelRatePlan::factory()->create();

    HotelRate::insert([

        'room_id' => $room->id,
        'rate_plan_id' => $ratePlan->id,
        'date' => $date,
        'base_rate' => 100,
        'currency_code' => 'BDT',
        'is_closed' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $results = $this->service->checkAvailability(
        $hotel->id,
        $date,
        now()->addDay()->format('Y-m-d'), // 1 night stay
        1, 2, 0
    );

    expect($results)->not->toBeEmpty();
    expect($results[0]['room']->id)->toBe($room->id);
    expect($results[0]['total_rate'])->toBe(100.0);
});
