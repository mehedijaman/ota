<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelBooking;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;

uses(\Tests\TestCase::class, RefreshDatabase::class);

use Illuminate\Support\Facades\Mail; // Add import if not exists or merge with others

beforeEach(function () {
    Mail::fake();
    $this->user = User::factory()->create();
    Role::create(['name' => 'superadmin']);
    $this->user->assignRole('superadmin');
    $this->hotel = Hotel::factory()->create();
});

describe('HotelBookingController', function () {

    it('can view booking index page', function () {
        actingAs($this->user)
            ->get(route('hotelBooking.index'))
            ->assertSuccessful();
    });

    it('can view booking create page', function () {
        actingAs($this->user)
            ->get(route('hotelBooking.create'))
            ->assertSuccessful();
    });

    it('can store a new booking', function () {
        $this->withoutExceptionHandling();
        $room = \Modules\Hotel\Models\HotelRoom::factory()->create(['hotel_id' => $this->hotel->id]);
        $ratePlan = \Modules\Hotel\Models\HotelRatePlan::factory()->create(['hotel_id' => $this->hotel->id]);

        $bookingData = [
            'hotel_id' => $this->hotel->id,
            'check_in_date' => now()->addDays(7)->format('Y-m-d'),
            'check_out_date' => now()->addDays(10)->format('Y-m-d'),
            'total_rooms' => 1,
            'total_adults' => 2,
            'total_children' => 0,
            'contact_name' => 'Test Guest',
            'contact_email' => 'test@example.com',
            'contact_phone' => '01700000000',
            'room_total' => 15000,
            'payment_status' => 'unpaid',
            'rooms' => [
                [
                    'room_id' => $room->id,
                    'rate_plan_id' => $ratePlan->id,
                    'adults' => 2,
                    'children' => 0,
                    'infants' => 0,
                ],
            ],
            'guests' => [],
        ];

        $response = actingAs($this->user)
            ->post(route('hotelBooking.store'), $bookingData);

        if (session('errors')) {
            dump('Booking Store Errors:', session('errors')->all());
        }

        $response->assertRedirect();

        $this->assertDatabaseHas('hotel_bookings', ['contact_name' => 'Test Guest']);
    });

    it('can view booking show page', function () {
        $booking = HotelBooking::factory()->create(['hotel_id' => $this->hotel->id]);

        actingAs($this->user)
            ->get(route('hotelBooking.show', $booking->id))
            ->assertSuccessful();
    });

    it('can view booking edit page', function () {
        $booking = HotelBooking::factory()->create(['hotel_id' => $this->hotel->id]);

        actingAs($this->user)
            ->get(route('hotelBooking.edit', $booking->id))
            ->assertSuccessful();
    });

    it('can update booking status to confirmed', function () {
        $booking = HotelBooking::factory()->create([
            'hotel_id' => $this->hotel->id,
            'status' => 'pending',
        ]);

        actingAs($this->user)
            ->patch(route('hotelBooking.updateStatus', ['id' => $booking->id, 'status' => 'confirmed']))
            ->assertRedirect();

        $this->assertDatabaseHas('hotel_bookings', [
            'id' => $booking->id,
            'status' => 'confirmed',
        ]);
    });

    it('can update booking status to checked_in', function () {
        $booking = HotelBooking::factory()->create([
            'hotel_id' => $this->hotel->id,
            'status' => 'confirmed',
        ]);

        actingAs($this->user)
            ->patch(route('hotelBooking.updateStatus', ['id' => $booking->id, 'status' => 'checked_in']))
            ->assertRedirect();

        $this->assertDatabaseHas('hotel_bookings', [
            'id' => $booking->id,
            'status' => 'checked_in',
        ]);
    });

    it('can update booking status to checked_out', function () {
        $booking = HotelBooking::factory()->create([
            'hotel_id' => $this->hotel->id,
            'status' => 'checked_in',
        ]);

        actingAs($this->user)
            ->patch(route('hotelBooking.updateStatus', ['id' => $booking->id, 'status' => 'checked_out']))
            ->assertRedirect();

        $this->assertDatabaseHas('hotel_bookings', [
            'id' => $booking->id,
            'status' => 'checked_out',
        ]);
    });

    it('can cancel a pending booking', function () {
        $booking = HotelBooking::factory()->create([
            'hotel_id' => $this->hotel->id,
            'status' => 'pending',
        ]);

        actingAs($this->user)
            ->patch(route('hotelBooking.updateStatus', ['id' => $booking->id, 'status' => 'cancelled']))
            ->assertRedirect();

        $this->assertDatabaseHas('hotel_bookings', [
            'id' => $booking->id,
            'status' => 'cancelled',
        ]);
    });

    it('can soft delete a booking', function () {
        $booking = HotelBooking::factory()->create(['hotel_id' => $this->hotel->id]);

        actingAs($this->user)
            ->delete(route('hotelBooking.destroy', $booking->id))
            ->assertRedirect();

        $this->assertSoftDeleted('hotel_bookings', ['id' => $booking->id]);
    });

    it('can view recycle bin', function () {
        actingAs($this->user)
            ->get(route('hotelBooking.recycleBin'))
            ->assertSuccessful();
    });

    it('can restore a deleted booking', function () {
        $booking = HotelBooking::factory()->create(['hotel_id' => $this->hotel->id]);
        $booking->delete();

        actingAs($this->user)
            ->post(route('hotelBooking.restore', $booking->id))
            ->assertRedirect();

        $this->assertDatabaseHas('hotel_bookings', ['id' => $booking->id, 'deleted_at' => null]);
    });
});
