<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Hotel\Models\Hotel;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;

uses(\Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Role::create(['name' => 'superadmin']);
    $this->user->assignRole('superadmin');
});

describe('HotelController', function () {

    it('can view hotel index page', function () {
        actingAs($this->user)
            ->get(route('hotel.index'))
            ->assertSuccessful();
    });

    it('can view hotel create page', function () {
        actingAs($this->user)
            ->get(route('hotel.create'))
            ->assertSuccessful();
    });

    it('can store a new hotel', function () {
        $country = \Modules\Hotel\Models\HotelCountry::factory()->create();
        $city = \Modules\Hotel\Models\HotelCity::factory()->create(['country_id' => $country->id]);
        $hotelData = [
            'name' => 'Test Hotel',
            'slug' => 'test-hotel',
            'star_rating' => 4,
            'property_type' => 'hotel',
            'country_id' => $country->id,
            'city_id' => $city->id,
            'address' => '123 Test Street',
            'check_in_time' => '14:00',
            'check_out_time' => '11:00',
            'is_active' => true,
        ];

        actingAs($this->user)
            ->post(route('hotel.store'), $hotelData)
            ->assertRedirect();

        $this->assertDatabaseHas('hotels', ['name' => 'Test Hotel']);
    });

    it('can view hotel show page', function () {
        $hotel = Hotel::factory()->create();

        actingAs($this->user)
            ->get(route('hotel.show', $hotel->id))
            ->assertSuccessful();
    });

    it('can view hotel edit page', function () {
        $hotel = Hotel::factory()->create();

        actingAs($this->user)
            ->get(route('hotel.edit', $hotel->id))
            ->assertSuccessful();
    });

    it('can update a hotel', function () {
        $hotel = Hotel::factory()->create();

        actingAs($this->user)
            ->put(route('hotel.update', $hotel->id), [
                'name' => 'Updated Hotel Name',
                'slug' => $hotel->slug,
                'star_rating' => $hotel->star_rating,
                'property_type' => $hotel->property_type,
                'country_id' => $hotel->country_id,
                'city_id' => $hotel->city_id,
                'address' => $hotel->address,
                'check_in_time' => $hotel->check_in_time?->format('H:i'),
                'check_out_time' => $hotel->check_out_time?->format('H:i'),
                'is_active' => true,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('hotels', ['name' => 'Updated Hotel Name']);
    });

    it('can soft delete a hotel', function () {
        $hotel = Hotel::factory()->create();

        actingAs($this->user)
            ->delete(route('hotel.destroy', $hotel->id))
            ->assertRedirect();

        $this->assertSoftDeleted('hotels', ['id' => $hotel->id]);
    });

    it('can view recycle bin', function () {
        actingAs($this->user)
            ->get(route('hotel.recycleBin'))
            ->assertSuccessful();
    });

    it('can restore a deleted hotel', function () {
        $hotel = Hotel::factory()->create();
        $hotel->delete();

        actingAs($this->user)
            ->post(route('hotel.restore', $hotel->id))
            ->assertRedirect();

        $this->assertDatabaseHas('hotels', ['id' => $hotel->id, 'deleted_at' => null]);
    });

    it('can force delete a hotel', function () {
        $hotel = Hotel::factory()->create();
        $hotel->delete();

        actingAs($this->user)
            ->delete(route('hotel.destroyForce', $hotel->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('hotels', ['id' => $hotel->id]);
    });
});
