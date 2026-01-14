<?php

namespace Modules\Hotel\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelRoom;
use Modules\Hotel\Models\HotelRoomType;
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

it('can view room index page', function () {
    HotelRoom::factory()->count(3)->create(['hotel_id' => $this->hotel->id]);

    actingAs($this->user)
        ->get(route('hotel.room.index', $this->hotel->id))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('HotelRoom/RoomIndex')
            ->has('rooms.data', 3)
        );
});

it('can view room create page', function () {
    actingAs($this->user)
        ->get(route('hotel.room.create', $this->hotel->id))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('HotelRoom/RoomForm')
        );
});

it('can store a new room', function () {
    $roomType = HotelRoomType::factory()->create();

    $roomData = [
        'hotel_id' => $this->hotel->id,
        'room_type_id' => $roomType->id,
        'name' => 'Deluxe Room',
        'bed_type' => 'king',
        'bed_count' => 1,
        'max_adults' => 2,
        'max_occupancy' => 3,
        'standard_occupancy' => 2,
        'total_inventory' => 5,
        'is_active' => true,
    ];

    actingAs($this->user)
        ->post(route('hotel.room.store', $this->hotel->id), $roomData)
        ->assertRedirect(route('hotel.room.index', $this->hotel->id));

    $this->assertDatabaseHas('hotel_rooms', ['name' => 'Deluxe Room', 'total_inventory' => 5]);
});

it('can view room edit page', function () {
    $room = HotelRoom::factory()->create(['hotel_id' => $this->hotel->id]);

    actingAs($this->user)
        ->get(route('hotel.room.edit', [$this->hotel->id, $room->id]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('HotelRoom/RoomForm')
            ->where('room.id', $room->id)
        );
});

it('can update a room', function () {
    $room = HotelRoom::factory()->create(['hotel_id' => $this->hotel->id, 'name' => 'Old Room']);

    $updateData = [
        'hotel_id' => $this->hotel->id,
        'room_type_id' => $room->room_type_id,
        'name' => 'Updated Room',
        'bed_type' => 'queen',
        'bed_count' => 1,
        'max_adults' => 2,
        'max_occupancy' => 2,
        'standard_occupancy' => 2,
        'total_inventory' => 10,
        'is_active' => true,
    ];

    actingAs($this->user)
        ->put(route('hotel.room.update', [$this->hotel->id, $room->id]), $updateData)
        ->assertRedirect(route('hotel.room.index', $this->hotel->id));

    $this->assertDatabaseHas('hotel_rooms', ['name' => 'Updated Room', 'total_inventory' => 10]);
});

it('can soft delete a room', function () {
    $room = HotelRoom::factory()->create(['hotel_id' => $this->hotel->id]);

    actingAs($this->user)
        ->delete(route('hotel.room.destroy', [$this->hotel->id, $room->id]))
        ->assertRedirect(route('hotel.room.index', $this->hotel->id));

    $this->assertSoftDeleted('hotel_rooms', ['id' => $room->id]);
});

it('can view room recycle bin', function () {
    HotelRoom::factory()->create(['hotel_id' => $this->hotel->id])->delete();

    actingAs($this->user)
        ->get(route('hotel.room.recycleBin', $this->hotel->id))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('HotelRoom/RoomRecycleBin')
            ->has('rooms.data', 1)
        );
});

it('can restore a deleted room', function () {
    $room = HotelRoom::factory()->create(['hotel_id' => $this->hotel->id]);
    $room->delete();

    actingAs($this->user)
        ->post(route('hotel.room.restore', [$this->hotel->id, $room->id]))
        ->assertRedirect(route('hotel.room.recycleBin', $this->hotel->id));

    $this->assertNotSoftDeleted('hotel_rooms', ['id' => $room->id]);
});
