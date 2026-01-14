<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelReview;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;

uses(\Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Role::create(['name' => 'superadmin']);
    $this->user->assignRole('superadmin');
    $this->hotel = Hotel::factory()->create();
});

describe('HotelReviewController', function () {

    it('can view review index page', function () {
        actingAs($this->user)
            ->get(route('hotelReview.index'))
            ->assertSuccessful();
    });

    it('can view review show page', function () {
        $review = HotelReview::factory()->create(['hotel_id' => $this->hotel->id]);

        actingAs($this->user)
            ->get(route('hotelReview.show', $review->id))
            ->assertSuccessful();
    });

    it('can approve a pending review', function () {
        $review = HotelReview::factory()->create([
            'hotel_id' => $this->hotel->id,
            'moderation_status' => 'pending',
        ]);

        actingAs($this->user)
            ->patch(route('hotelReview.approve', $review->id))
            ->assertRedirect();

        $this->assertDatabaseHas('hotel_reviews', [
            'id' => $review->id,
            'moderation_status' => 'approved',
        ]);
    });

    it('can reject a pending review', function () {
        $review = HotelReview::factory()->create([
            'hotel_id' => $this->hotel->id,
            'moderation_status' => 'pending',
        ]);

        actingAs($this->user)
            ->patch(route('hotelReview.reject', $review->id), ['reason' => 'Inappropriate content'])
            ->assertRedirect();

        $this->assertDatabaseHas('hotel_reviews', [
            'id' => $review->id,
            'moderation_status' => 'rejected',
        ]);
    });

    it('can toggle featured status', function () {
        $review = HotelReview::factory()->create([
            'hotel_id' => $this->hotel->id,
            'is_featured' => false,
        ]);

        actingAs($this->user)
            ->patch(route('hotelReview.toggleFeatured', $review->id))
            ->assertRedirect();

        $this->assertDatabaseHas('hotel_reviews', [
            'id' => $review->id,
            'is_featured' => true,
        ]);
    });

    it('can soft delete a review', function () {
        $review = HotelReview::factory()->create(['hotel_id' => $this->hotel->id]);

        actingAs($this->user)
            ->delete(route('hotelReview.destroy', $review->id))
            ->assertRedirect();

        $this->assertSoftDeleted('hotel_reviews', ['id' => $review->id]);
    });

    it('can view recycle bin', function () {
        actingAs($this->user)
            ->get(route('hotelReview.recycleBin'))
            ->assertSuccessful();
    });

    it('can restore a deleted review', function () {
        $review = HotelReview::factory()->create(['hotel_id' => $this->hotel->id]);
        $review->delete();

        actingAs($this->user)
            ->post(route('hotelReview.restore', $review->id))
            ->assertRedirect();

        $this->assertDatabaseHas('hotel_reviews', ['id' => $review->id, 'deleted_at' => null]);
    });
});
