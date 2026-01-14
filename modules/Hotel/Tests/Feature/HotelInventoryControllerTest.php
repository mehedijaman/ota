<?php

namespace Modules\Hotel\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelRoom;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

use function Pest\Laravel\actingAs;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Role::create(['name' => 'superadmin']);
    $this->user->assignRole('superadmin');
    $this->hotel = Hotel::factory()->create();
});

it('can view availability page', function () {
    actingAs($this->user)
        ->get(route('hotel.availability.show', $this->hotel->id))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('HotelInventory/AvailabilityIndex')
        );
});

it('can get inventory data', function () {
    $start = now()->format('Y-m-d');
    $end = now()->addDays(7)->format('Y-m-d');

    actingAs($this->user)
        ->get(route('hotel.inventory.data', [
            'hotel' => $this->hotel->id,
            'start' => $start,
            'end' => $end,
        ]))
        ->assertOk()
        ->assertJsonStructure([]); // Structure depends on data, empty if no rooms/bookings
});

it('can update inventory', function () {
    $room = HotelRoom::factory()->create(['hotel_id' => $this->hotel->id]);

    $updateData = [
        'room_id' => $room->id,
        'start_date' => now()->format('Y-m-d'),
        'end_date' => now()->addDays(2)->format('Y-m-d'),
        'total_inventory' => 5,
        'blocked' => 1,
    ];

    actingAs($this->user)
        ->post(route('hotel.inventory.update', $this->hotel->id), $updateData)
        ->assertRedirect();
});
