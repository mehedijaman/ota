<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Customer\Models\Customer;
use Modules\Tour\Models\Tour;
use Modules\Tour\Models\TourBooking;
use Modules\Tour\Models\TourCategory;
use Modules\Tour\Models\TourDestination;
use Modules\Tour\Models\TourPricing;
use Modules\Tour\Models\TourReview;
use Modules\Tour\Services\TourBookingService;
use Modules\Tour\Services\TourCategoryService;
use Modules\Tour\Services\TourDestinationService;
use Modules\Tour\Services\TourReviewService;
use Modules\Tour\Services\TourService;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->customer = Customer::factory()->create();
    TourCategory::factory()->create();
    TourDestination::factory()->create();
});

describe('TourCategoryService', function () {
    it('can create a category', function () {
        $service = app(TourCategoryService::class);

        $category = $service->create([
            'name' => 'Beach Tours',
            'slug' => 'beach-tours',
            'is_active' => true,
        ]);

        expect($category)->toBeInstanceOf(TourCategory::class)
            ->and($category->name)->toBe('Beach Tours');
    });

    it('can update a category', function () {
        $service = app(TourCategoryService::class);
        $category = TourCategory::factory()->create();

        $updated = $service->update($category, [
            'name' => 'Updated Name',
            'slug' => 'updated-name',
        ]);

        expect($updated->name)->toBe('Updated Name');
    });

    it('cannot delete category with tours', function () {
        $service = app(TourCategoryService::class);
        $category = TourCategory::factory()->create();
        Tour::factory()->create(['category_id' => $category->id]);

        $result = $service->delete($category);

        expect($result)->toBeFalse()
            ->and(TourCategory::find($category->id))->not->toBeNull();
    });

    it('can delete category without tours', function () {
        $service = app(TourCategoryService::class);
        $category = TourCategory::factory()->create();

        $result = $service->delete($category);

        expect($result)->toBeTrue();
        $this->assertSoftDeleted('tour_categories', ['id' => $category->id]);
    });

    it('can restore deleted category', function () {
        $service = app(TourCategoryService::class);
        $category = TourCategory::factory()->create();
        $category->delete();

        $service->restore($category->id);

        $this->assertDatabaseHas('tour_categories', [
            'id' => $category->id,
            'deleted_at' => null,
        ]);
    });

    it('can force delete category', function () {
        $service = app(TourCategoryService::class);
        $category = TourCategory::factory()->create();
        $category->delete();

        $service->forceDelete($category->id);

        $this->assertDatabaseMissing('tour_categories', ['id' => $category->id]);
    });
});

describe('TourDestinationService', function () {
    it('can create a destination', function () {
        $service = app(TourDestinationService::class);

        $destination = $service->create([
            'name' => "Cox's Bazar",
            'slug' => 'coxs-bazar',
            'country' => 'Bangladesh',
            'is_active' => true,
        ]);

        expect($destination)->toBeInstanceOf(TourDestination::class)
            ->and($destination->name)->toBe("Cox's Bazar");
    });

    it('can update a destination', function () {
        $service = app(TourDestinationService::class);
        $destination = TourDestination::factory()->create();

        $updated = $service->update($destination, [
            'name' => 'Updated Destination',
            'slug' => 'updated-destination',
        ]);

        expect($updated->name)->toBe('Updated Destination');
    });

    it('cannot delete destination with tours', function () {
        $service = app(TourDestinationService::class);
        $destination = TourDestination::factory()->create();
        Tour::factory()->create(['destination_id' => $destination->id]);

        $result = $service->delete($destination);

        expect($result)->toBeFalse()
            ->and(TourDestination::find($destination->id))->not->toBeNull();
    });
});

describe('TourService', function () {
    it('can create a tour', function () {
        $service = app(TourService::class);
        $category = TourCategory::first();
        $destination = TourDestination::first();

        $tour = $service->create([
            'name' => 'Beach Adventure',
            'slug' => 'beach-adventure',
            'category_id' => $category->id,
            'destination_id' => $destination->id,
            'duration_days' => 3,
            'duration_nights' => 2,
            'is_active' => true,
        ]);

        expect($tour)->toBeInstanceOf(Tour::class)
            ->and($tour->name)->toBe('Beach Adventure');
    });

    it('can update a tour', function () {
        $service = app(TourService::class);
        $tour = Tour::factory()->create();

        $updated = $service->update($tour, [
            'name' => 'Updated Tour',
            'slug' => 'updated-tour',
        ]);

        expect($updated->name)->toBe('Updated Tour');
    });

    it('can delete a tour', function () {
        $service = app(TourService::class);
        $tour = Tour::factory()->create();

        $service->delete($tour);

        $this->assertSoftDeleted('tours', ['id' => $tour->id]);
    });

    it('can restore deleted tour', function () {
        $service = app(TourService::class);
        $tour = Tour::factory()->create();
        $tour->delete();

        $service->restore($tour->id);

        $this->assertDatabaseHas('tours', [
            'id' => $tour->id,
            'deleted_at' => null,
        ]);
    });
});

describe('TourBookingService', function () {
    beforeEach(function () {
        $this->tour = Tour::factory()->create();
        $this->pricing = TourPricing::factory()->create([
            'tour_id' => $this->tour->id,
            'adult_price' => 5000,
            'child_price' => 3000,
            'infant_price' => 1000,
        ]);
    });

    it('calculates pricing correctly', function () {
        $service = app(TourBookingService::class);

        $pricing = $service->calculatePricing([
            'tour_id' => $this->tour->id,
            'pricing_id' => $this->pricing->id,
            'adult_count' => 2,
            'child_count' => 1,
            'infant_count' => 0,
        ]);

        // 2 * 5000 + 1 * 3000 = 13000
        expect($pricing['subtotal'])->toBe(13000.0)
            ->and($pricing['total_amount'])->toBe(13000.0);
    });

    it('calculates pricing with custom subtotal', function () {
        $service = app(TourBookingService::class);

        $pricing = $service->calculatePricing([
            'tour_id' => $this->tour->id,
            'pricing_id' => $this->pricing->id,
            'adult_count' => 2,
            'child_count' => 1,
            'infant_count' => 0,
            'subtotal' => 15000,
        ]);

        expect($pricing['subtotal'])->toBe(15000.0)
            ->and($pricing['total_amount'])->toBe(15000.0);
    });

    it('calculates pricing with discount', function () {
        $service = app(TourBookingService::class);

        $pricing = $service->calculatePricing([
            'tour_id' => $this->tour->id,
            'pricing_id' => $this->pricing->id,
            'adult_count' => 2,
            'child_count' => 0,
            'infant_count' => 0,
            'discount_amount' => 1000,
        ]);

        // 2 * 5000 = 10000 - 1000 discount = 9000
        expect($pricing['subtotal'])->toBe(10000.0)
            ->and($pricing['discount_amount'])->toBe(1000.0)
            ->and($pricing['total_amount'])->toBe(9000.0);
    });

    it('can update booking status', function () {
        $service = app(TourBookingService::class);
        $booking = TourBooking::factory()->pending()->create([
            'tour_id' => $this->tour->id,
            'pricing_id' => $this->pricing->id,
            'customer_id' => $this->customer->id,
        ]);

        $updated = $service->updateStatus($booking, 'confirmed');

        expect($updated->status)->toBe('confirmed')
            ->and($updated->confirmed_at)->not->toBeNull();
    });

    it('can cancel booking', function () {
        $service = app(TourBookingService::class);
        $booking = TourBooking::factory()->confirmed()->create([
            'tour_id' => $this->tour->id,
            'pricing_id' => $this->pricing->id,
            'customer_id' => $this->customer->id,
        ]);

        $updated = $service->updateStatus($booking, 'cancelled');

        expect($updated->status)->toBe('cancelled')
            ->and($updated->cancelled_at)->not->toBeNull();
    });
});

describe('TourReviewService', function () {
    beforeEach(function () {
        $this->tour = Tour::factory()->create();
    });

    it('can approve a review', function () {
        $service = app(TourReviewService::class);
        $review = TourReview::factory()->create([
            'tour_id' => $this->tour->id,
            'is_approved' => false,
        ]);

        $approved = $service->approve($review);

        expect($approved->is_approved)->toBeTrue()
            ->and($approved->approved_at)->not->toBeNull();
    });

    it('can reject a review', function () {
        $service = app(TourReviewService::class);
        $review = TourReview::factory()->approved()->create([
            'tour_id' => $this->tour->id,
        ]);

        $rejected = $service->reject($review);

        expect($rejected->is_approved)->toBeFalse()
            ->and($rejected->approved_at)->toBeNull();
    });

    it('can toggle featured status', function () {
        $service = app(TourReviewService::class);
        $review = TourReview::factory()->create([
            'tour_id' => $this->tour->id,
            'is_featured' => false,
        ]);

        $toggled = $service->toggleFeatured($review);
        expect($toggled->is_featured)->toBeTrue();

        $toggledAgain = $service->toggleFeatured($review);
        expect($toggledAgain->is_featured)->toBeFalse();
    });

    it('can get trashed reviews', function () {
        $service = app(TourReviewService::class);
        TourReview::factory()->count(3)->create(['tour_id' => $this->tour->id])
            ->each->delete();

        $trashed = $service->getTrashed();

        expect($trashed->total())->toBe(3);
    });

    it('can restore a review', function () {
        $service = app(TourReviewService::class);
        $review = TourReview::factory()->create(['tour_id' => $this->tour->id]);
        $review->delete();

        $service->restore($review);

        $this->assertDatabaseHas('tour_reviews', [
            'id' => $review->id,
            'deleted_at' => null,
        ]);
    });

    it('can empty recycle bin', function () {
        $service = app(TourReviewService::class);
        TourReview::factory()->count(3)->create(['tour_id' => $this->tour->id])
            ->each->delete();

        $count = $service->emptyRecycleBin();

        expect($count)->toBe(3)
            ->and(TourReview::onlyTrashed()->count())->toBe(0);
    });
});
