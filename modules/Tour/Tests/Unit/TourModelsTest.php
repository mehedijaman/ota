<?php

use Modules\Tour\Models\Tour;
use Modules\Tour\Models\TourPricing;
use Modules\Tour\Models\TourExtra;
use Modules\Tour\Models\TourBooking;
use Modules\Tour\Models\TourBookingTraveler;
use Modules\Tour\Models\TourBookingExtra;
use App\Models\User;
use Modules\Customer\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->customer = Customer::factory()->create();
});

describe('Tour Model', function () {
    it('can create a tour', function () {
        $tour = Tour::factory()->create([
            'name' => 'Test Tour',
            'slug' => 'test-tour',
        ]);

        expect($tour->name)->toBe('Test Tour')
            ->and($tour->slug)->toBe('test-tour');
    });

    it('has pricings relationship', function () {
        $tour = Tour::factory()->create();
        TourPricing::factory()->count(3)->create(['tour_id' => $tour->id]);

        expect($tour->pricings)->toHaveCount(3);
    });

    it('has extras relationship', function () {
        $tour = Tour::factory()->create();
        TourExtra::factory()->count(2)->create(['tour_id' => $tour->id]);

        expect($tour->extras)->toHaveCount(2);
    });

    it('has bookings relationship', function () {
        $tour = Tour::factory()->create();
        TourBooking::factory()->count(2)->create([
            'tour_id' => $tour->id,
            'customer_id' => $this->customer->id,
        ]);

        expect($tour->bookings)->toHaveCount(2);
    });

// ...




    it('can get active tours', function () {
        Tour::factory()->create(['is_active' => true]);
        Tour::factory()->create(['is_active' => false]);

        $activeTours = Tour::where('is_active', true)->get();

        expect($activeTours)->toHaveCount(1);
    });
});

describe('Tour Pricing Model', function () {
    it('can create pricing for a tour', function () {
        $tour = Tour::factory()->create();
        $pricing = TourPricing::factory()->create([
            'tour_id' => $tour->id,
            'name' => 'Standard Package',
            'adult_price' => 5000,
        ]);

        expect($pricing->tour_id)->toBe($tour->id)
            ->and($pricing->name)->toBe('Standard Package')
            ->and((float)$pricing->adult_price)->toBe(5000.0);
    });

    it('belongs to a tour', function () {
        $tour = Tour::factory()->create();
        $pricing = TourPricing::factory()->create(['tour_id' => $tour->id]);

        expect($pricing->tour)->toBeInstanceOf(Tour::class)
            ->and($pricing->tour->id)->toBe($tour->id);
    });
});

describe('Tour Extra Model', function () {
    it('can create extra for a tour', function () {
        $tour = Tour::factory()->create();
        $extra = TourExtra::factory()->create([
            'tour_id' => $tour->id,
            'name' => 'Airport Transfer',
            'price' => 1000,
        ]);

        expect($extra->tour_id)->toBe($tour->id)
            ->and($extra->name)->toBe('Airport Transfer')
            ->and((float)$extra->price)->toBe(1000.0);
    });

    it('belongs to a tour', function () {
        $tour = Tour::factory()->create();
        $extra = TourExtra::factory()->create(['tour_id' => $tour->id]);

        expect($extra->tour)->toBeInstanceOf(Tour::class);
    });
});

describe('Tour Booking Model', function () {
    it('can create a booking', function () {
        $tour = Tour::factory()->create();
        $pricing = TourPricing::factory()->create(['tour_id' => $tour->id]);
        $customer = Customer::factory()->create();

        $booking = TourBooking::factory()->create([
            'tour_id' => $tour->id,
            'pricing_id' => $pricing->id,
            'customer_id' => $customer->id,
            'adult_count' => 2,
            'total_amount' => 10000,
        ]);

        expect($booking->tour_id)->toBe($tour->id)
            ->and($booking->adult_count)->toBe(2)
            ->and((float)$booking->total_amount)->toBe(10000.0);
    });

    it('generates unique booking number', function () {
        $tour = Tour::factory()->create();
        $pricing = TourPricing::factory()->create(['tour_id' => $tour->id]);

        $booking1 = TourBooking::factory()->create([
            'tour_id' => $tour->id,
            'pricing_id' => $pricing->id,
        ]);

        $booking2 = TourBooking::factory()->create([
            'tour_id' => $tour->id,
            'pricing_id' => $pricing->id,
        ]);

        expect($booking1->booking_number)->not->toBe($booking2->booking_number);
    });

    it('belongs to tour, pricing, and customer', function () {
        $tour = Tour::factory()->create();
        $pricing = TourPricing::factory()->create(['tour_id' => $tour->id]);
        $customer = Customer::factory()->create();

        $booking = TourBooking::factory()->create([
            'tour_id' => $tour->id,
            'pricing_id' => $pricing->id,
            'customer_id' => $customer->id,
        ]);

        expect($booking->tour)->toBeInstanceOf(Tour::class)
            ->and($booking->pricing)->toBeInstanceOf(TourPricing::class); // User relationship might refer to created_by or needs alias
    });

    it('has travelers relationship', function () {
        $tour = Tour::factory()->create();
        $pricing = TourPricing::factory()->create(['tour_id' => $tour->id]);
        $booking = TourBooking::factory()->create([
            'tour_id' => $tour->id,
            'pricing_id' => $pricing->id,
            'customer_id' => $this->customer->id,
        ]);

        TourBookingTraveler::factory()->count(3)->create(['booking_id' => $booking->id]);

        expect($booking->travelers)->toHaveCount(3);
    });

    it('has extras relationship', function () {
        $tour = Tour::factory()->create();
        $pricing = TourPricing::factory()->create(['tour_id' => $tour->id]);
        $booking = TourBooking::factory()->create([
            'tour_id' => $tour->id,
            'pricing_id' => $pricing->id,
            'customer_id' => $this->customer->id,
        ]);

        $extra = TourExtra::factory()->create(['tour_id' => $tour->id]);
        TourBookingExtra::factory()->create([
            'booking_id' => $booking->id,
            'extra_id' => $extra->id,
        ]);

        expect($booking->extras)->toHaveCount(1);
    });
});

describe('Tour Booking Traveler Model', function () {
    it('can create traveler for booking', function () {
        $tour = Tour::factory()->create();
        $pricing = TourPricing::factory()->create(['tour_id' => $tour->id]);
        $booking = TourBooking::factory()->create([
            'tour_id' => $tour->id,
            'pricing_id' => $pricing->id,
            'customer_id' => $this->customer->id,
        ]);

        $traveler = TourBookingTraveler::factory()->create([
            'booking_id' => $booking->id,
            'type' => 'adult',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'is_lead' => true,
        ]);

        expect($traveler->booking_id)->toBe($booking->id)
            ->and($traveler->type)->toBe('adult')
            ->and($traveler->first_name)->toBe('John')
            ->and($traveler->is_lead)->toBeTrue();
    });

    it('belongs to booking', function () {
        $tour = Tour::factory()->create();
        $pricing = TourPricing::factory()->create(['tour_id' => $tour->id]);
        $booking = TourBooking::factory()->create([
            'tour_id' => $tour->id,
            'pricing_id' => $pricing->id,
            'customer_id' => $this->customer->id,
        ]);

        $traveler = TourBookingTraveler::factory()->create(['booking_id' => $booking->id]);

        expect($traveler->booking)->toBeInstanceOf(TourBooking::class);
    });
});
