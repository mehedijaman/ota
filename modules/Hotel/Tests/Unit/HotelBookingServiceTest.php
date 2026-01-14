<?php

namespace Modules\Hotel\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Modules\Hotel\Mail\HotelBookingConfirmed;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelBooking;
use Modules\Hotel\Models\HotelRatePlan;
use Modules\Hotel\Models\HotelRoom;
use Modules\Hotel\Services\HotelBookingService;
use Tests\TestCase;

class HotelBookingServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected HotelBookingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new HotelBookingService;
    }

    public function test_get_all_returns_paginated_results()
    {
        // Use fewer records to avoid running out of unique countries in factory
        HotelBooking::factory()->count(5)->create();

        $results = $this->service->getAll();

        $this->assertInstanceOf(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class, $results);
        $this->assertGreaterThan(0, $results->total());
    }

    public function test_get_stats_returns_correct_counts()
    {
        HotelBooking::factory()->count(2)->state(['status' => 'pending'])->create();
        HotelBooking::factory()->count(1)->state(['status' => 'confirmed'])->create();

        $stats = $this->service->getStats();

        $this->assertEquals(3, $stats['total']);
        $this->assertEquals(2, $stats['pending']);
        $this->assertEquals(1, $stats['confirmed']);
    }

    public function test_create_stores_booking_and_rooms()
    {
        $hotel = Hotel::factory()->create();
        $room = HotelRoom::factory()->create(['hotel_id' => $hotel->id]);
        $ratePlan = HotelRatePlan::factory()->create();

        $data = [
            'hotel_id' => $hotel->id,
            'check_in_date' => now()->addDays(1)->format('Y-m-d'),
            'check_out_date' => now()->addDays(3)->format('Y-m-d'),
            'total_adults' => 2,
            'contact_name' => 'John Doe',
            'contact_email' => 'john@example.com',
            'contact_phone' => '1234567890',
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'currency_code' => 'USD',
            'total_amount' => 200,
            'amount_due' => 200,
            'rooms' => [
                [
                    'room_id' => $room->id,
                    'rate_plan_id' => $ratePlan->id,
                    'quantity' => 1,
                    'adults' => 2,
                    'room_rate' => 100,
                    'total_amount' => 200,
                    'guests' => [
                        ['first_name' => 'John', 'last_name' => 'Doe', 'type' => 'adult', 'is_primary' => true],
                        ['first_name' => 'Jane', 'last_name' => 'Doe', 'type' => 'adult'],
                    ],
                ],
            ],
        ];

        $booking = $this->service->create($data);

        $this->assertDatabaseHas('hotel_bookings', [
            'id' => $booking->id,
            'contact_email' => 'john@example.com',
            'nights' => 2,
        ]);

        $this->assertDatabaseHas('hotel_booking_rooms', [
            'booking_id' => $booking->id,
            'room_id' => $room->id,
        ]);

        $this->assertDatabaseHas('hotel_booking_guests', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'is_primary' => true,
        ]);
    }

    public function test_update_status_updates_booking_and_sends_email()
    {
        Mail::fake();

        $booking = HotelBooking::factory()->create([
            'status' => 'pending',
            'contact_email' => 'test@example.com',
        ]);

        $updated = $this->service->updateStatus($booking, 'confirmed');

        $this->assertEquals('confirmed', $updated->status);
        $this->assertNotNull($updated->confirmed_at);

        Mail::assertQueued(HotelBookingConfirmed::class);
    }

    public function test_add_payment_creates_payment_record()
    {
        $booking = HotelBooking::factory()->create([
            'total_amount' => 100,
            'amount_paid' => 0,
            'amount_due' => 100,
        ]);

        $payment = $this->service->addPayment($booking, [
            'amount' => 50,
            'method' => 'cash',
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('hotel_booking_payments', [
            'id' => $payment->id,
            'amount' => 50,
        ]);

        $this->assertNotNull($payment->paid_at);

        $booking->refresh();
        $this->assertEquals(50, $booking->amount_paid);
    }

    public function test_bulk_delete_moves_bookings_to_trash()
    {
        // Use 2 bookings to save CountryFactory uniques
        $bookings = HotelBooking::factory()->count(2)->create();
        $ids = $bookings->pluck('id')->toArray();

        $count = $this->service->bulkDelete($ids);

        $this->assertEquals(2, $count);
        $this->assertSoftDeleted('hotel_bookings', ['id' => $ids[0]]);
    }
}
