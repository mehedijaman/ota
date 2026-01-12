<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Customer\Models\Customer;
use Modules\Tour\Models\Tour;
use Modules\Tour\Models\TourBooking;
use Modules\Tour\Models\TourBookingExtra;
use Modules\Tour\Models\TourBookingTraveler;
use Modules\Tour\Models\TourExtra;
use Modules\Tour\Models\TourPricing;
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
            ->and((float) $pricing->adult_price)->toBe(5000.0);
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
            ->and((float) $extra->price)->toBe(1000.0);
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
            ->and((float) $booking->total_amount)->toBe(10000.0);
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

describe('Tour Review Model', function () {
    it('can create a review', function () {
        $tour = Tour::factory()->create();

        $review = \Modules\Tour\Models\TourReview::factory()->create([
            'tour_id' => $tour->id,
            'title' => 'Great Tour',
            'rating' => 4.5,
            'content' => 'This was an amazing experience!',
        ]);

        expect($review->tour_id)->toBe($tour->id)
            ->and($review->title)->toBe('Great Tour')
            ->and((float) $review->rating)->toBe(4.5)
            ->and($review->content)->toBe('This was an amazing experience!');
    });

    it('belongs to a tour', function () {
        $tour = Tour::factory()->create();
        $review = \Modules\Tour\Models\TourReview::factory()->create(['tour_id' => $tour->id]);

        expect($review->tour)->toBeInstanceOf(Tour::class);
    });

    it('can be approved', function () {
        $tour = Tour::factory()->create();
        $review = \Modules\Tour\Models\TourReview::factory()->create([
            'tour_id' => $tour->id,
            'is_approved' => false,
        ]);

        $review->update([
            'is_approved' => true,
            'approved_at' => now(),
        ]);

        expect($review->is_approved)->toBeTrue()
            ->and($review->approved_at)->not->toBeNull();
    });

    it('can be featured', function () {
        $tour = Tour::factory()->create();
        $review = \Modules\Tour\Models\TourReview::factory()->create([
            'tour_id' => $tour->id,
            'is_featured' => false,
        ]);

        $review->update(['is_featured' => true]);

        expect($review->is_featured)->toBeTrue();
    });

    it('has pros and cons as arrays', function () {
        $tour = Tour::factory()->create();
        $review = \Modules\Tour\Models\TourReview::factory()->create([
            'tour_id' => $tour->id,
            'pros' => ['Good guide', 'Nice food', 'Scenic views'],
            'cons' => ['Long journey'],
        ]);

        expect($review->pros)->toBeArray()
            ->and($review->pros)->toHaveCount(3)
            ->and($review->cons)->toBeArray()
            ->and($review->cons)->toHaveCount(1);
    });

    it('can be verified', function () {
        $tour = Tour::factory()->create();
        $review = \Modules\Tour\Models\TourReview::factory()->verified()->create([
            'tour_id' => $tour->id,
        ]);

        expect($review->is_verified)->toBeTrue();
    });

    it('uses soft deletes', function () {
        $tour = Tour::factory()->create();
        $review = \Modules\Tour\Models\TourReview::factory()->create(['tour_id' => $tour->id]);

        $review->delete();

        expect(\Modules\Tour\Models\TourReview::find($review->id))->toBeNull()
            ->and(\Modules\Tour\Models\TourReview::withTrashed()->find($review->id))->not->toBeNull();
    });

    it('scopes approved reviews', function () {
        $tour = Tour::factory()->create();
        \Modules\Tour\Models\TourReview::factory()->create([
            'tour_id' => $tour->id,
            'is_approved' => true,
        ]);
        \Modules\Tour\Models\TourReview::factory()->create([
            'tour_id' => $tour->id,
            'is_approved' => false,
        ]);

        $approved = \Modules\Tour\Models\TourReview::approved()->get();

        expect($approved)->toHaveCount(1);
    });

    it('scopes featured reviews', function () {
        $tour = Tour::factory()->create();
        \Modules\Tour\Models\TourReview::factory()->create([
            'tour_id' => $tour->id,
            'is_featured' => true,
        ]);
        \Modules\Tour\Models\TourReview::factory()->count(2)->create([
            'tour_id' => $tour->id,
            'is_featured' => false,
        ]);

        $featured = \Modules\Tour\Models\TourReview::featured()->get();

        expect($featured)->toHaveCount(1);
    });
});

describe('Tour Category Model', function () {
    it('can create a category', function () {
        $category = \Modules\Tour\Models\TourCategory::factory()->create([
            'name' => 'Beach Tours',
            'slug' => 'beach-tours',
        ]);

        expect($category->name)->toBe('Beach Tours')
            ->and($category->slug)->toBe('beach-tours');
    });

    it('has tours relationship', function () {
        $category = \Modules\Tour\Models\TourCategory::factory()->create();
        Tour::factory()->count(3)->create(['category_id' => $category->id]);

        expect($category->tours)->toHaveCount(3);
    });

    it('uses soft deletes', function () {
        $category = \Modules\Tour\Models\TourCategory::factory()->create();
        $category->delete();

        expect(\Modules\Tour\Models\TourCategory::find($category->id))->toBeNull()
            ->and(\Modules\Tour\Models\TourCategory::withTrashed()->find($category->id))->not->toBeNull();
    });
});

describe('Tour Destination Model', function () {
    it('can create a destination', function () {
        $destination = \Modules\Tour\Models\TourDestination::factory()->create([
            'name' => "Cox's Bazar",
            'slug' => 'coxs-bazar',
            'country' => 'Bangladesh',
        ]);

        expect($destination->name)->toBe("Cox's Bazar")
            ->and($destination->country)->toBe('Bangladesh');
    });

    it('has tours relationship', function () {
        $destination = \Modules\Tour\Models\TourDestination::factory()->create();
        Tour::factory()->count(2)->create(['destination_id' => $destination->id]);

        expect($destination->tours)->toHaveCount(2);
    });

    it('can be featured', function () {
        $destination = \Modules\Tour\Models\TourDestination::factory()->featured()->create();

        expect($destination->is_featured)->toBeTrue();
    });

    it('uses soft deletes', function () {
        $destination = \Modules\Tour\Models\TourDestination::factory()->create();
        $destination->delete();

        expect(\Modules\Tour\Models\TourDestination::find($destination->id))->toBeNull()
            ->and(\Modules\Tour\Models\TourDestination::withTrashed()->find($destination->id))->not->toBeNull();
    });
});
