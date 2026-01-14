<?php

namespace Modules\Hotel\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelRoom;
use Modules\Hotel\Services\HotelInventoryService;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->service = new HotelInventoryService;
    $this->hotel = Hotel::factory()->create();
});

it('fetches calendar data including default values', function () {
    $room = HotelRoom::factory()->create(['hotel_id' => $this->hotel->id, 'total_inventory' => 10]);
    $start = now()->format('Y-m-d');
    $end = now()->addDay()->format('Y-m-d');

    // No explicit inventory created.
    // Service should return default structure (not persisted).

    $data = $this->service->getCalendarData($this->hotel->id, $start, $end);

    expect($data)->toHaveCount(1);
    expect($data[0]['id'])->toBe($room->id);
    expect($data[0]['inventory_data'][$start]['total_inventory'])->toBe(10);
});

it('updates inventory via bulk update', function () {
    $room = HotelRoom::factory()->create(['hotel_id' => $this->hotel->id]);
    $date = now()->format('Y-m-d');

    $this->service->bulkUpdate($this->hotel->id, [
        'room_id' => $room->id,
        'start_date' => $date,
        'end_date' => $date,
        'total_inventory' => 20,
        'blocked' => 5,
    ]);

    $this->assertDatabaseHas('hotel_inventories', [
        'room_id' => $room->id,
        'total_inventory' => 20,
        'blocked' => 5,
    ]);
});
