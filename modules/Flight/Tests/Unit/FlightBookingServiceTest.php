<?php

use Modules\Flight\Models\FlightBooking;
use Modules\Flight\Models\FlightBookingPassenger;
use Modules\Flight\Models\FlightBookingPayment;
use Modules\Flight\Models\FlightBookingSegment;
use Modules\Flight\Services\FlightBookingService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    \Illuminate\Support\Facades\Mail::fake();
    $this->service = new FlightBookingService;
});

describe('FlightBookingService', function () {
    it('can create a booking', function () {
        $data = [
            'trip_type' => 'one_way',
            'departure_date' => now()->addDays(7)->toDateString(),
            'adult_count' => 1,
            'child_count' => 0,
            'infant_count' => 0,
            'contact_title' => 'Mr',
            'contact_first_name' => 'Test',
            'contact_last_name' => 'User',
            'contact_email' => 'test@example.com',
            'contact_phone_country_code' => '+880',
            'contact_phone' => '1712345678',
            'booking_source' => 'website',
        ];

        $booking = $this->service->create($data);

        expect($booking)->toBeInstanceOf(FlightBooking::class);
        expect($booking->booking_number)->toStartWith('FLT-');
        expect($booking->contact_email)->toBe('test@example.com');
        expect($booking->total_passengers)->toBe(1);
    });

    it('generates unique booking numbers', function () {
        $bookingNumbers = collect();

        for ($i = 0; $i < 10; $i++) {
            $booking = $this->service->create([
                'trip_type' => 'one_way',
                'departure_date' => now()->addDays(7)->toDateString(),
                'adult_count' => 1,
                'contact_title' => 'Mr',
                'contact_first_name' => 'Test',
                'contact_last_name' => 'User',
                'contact_email' => "test{$i}@example.com",
                'contact_phone_country_code' => '+880',
                'contact_phone' => '1712345678',
            ]);
            $bookingNumbers->push($booking->booking_number);
        }

        expect($bookingNumbers->unique()->count())->toBe(10);
    });

    it('can update booking status', function () {
        $booking = FlightBooking::factory()->create(['status' => 'pending']);

        $updatedBooking = $this->service->updateStatus($booking, 'confirmed');

        expect($updatedBooking->status)->toBe('confirmed');
        expect($updatedBooking->confirmed_at)->not->toBeNull();
    });

    it('can add payment to booking', function () {
        $booking = FlightBooking::factory()->create([
            'total_amount' => 10000,
            'amount_paid' => 0,
            'amount_due' => 10000,
        ]);

        $payment = $this->service->addPayment($booking, [
            'type' => 'payment',
            'method' => 'cash',
            'amount' => 5000,
            'status' => 'completed',
        ]);

        expect($payment)->toBeInstanceOf(FlightBookingPayment::class);
        expect($payment->amount)->toEqual(5000);
        expect($payment->method)->toBe('cash');
    });

    it('can get booking by id with relationships', function () {
        $booking = FlightBooking::factory()
            ->has(FlightBookingSegment::factory()->count(2), 'segments')
            ->has(FlightBookingPassenger::factory()->count(2), 'passengers')
            ->create();

        $result = $this->service->getById($booking->id);

        expect($result->segments)->toHaveCount(2);
        expect($result->passengers)->toHaveCount(2);
    });

    it('can get booking stats', function () {
        FlightBooking::factory()->count(3)->create(['status' => 'pending']);
        FlightBooking::factory()->count(2)->create(['status' => 'confirmed']);
        FlightBooking::factory()->count(1)->create(['status' => 'cancelled']);

        $stats = $this->service->getStats();

        expect($stats['total'])->toBe(6);
        expect($stats['pending'])->toBe(3);
        expect($stats['confirmed'])->toBe(2);
        expect($stats['cancelled'])->toBe(1);
    });
});
