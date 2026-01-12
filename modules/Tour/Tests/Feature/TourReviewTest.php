<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Tour\Models\Tour;
use Modules\Tour\Models\TourCategory;
use Modules\Tour\Models\TourDestination;
use Modules\Tour\Models\TourReview;
use Tests\TestCase;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\patch;
use function Pest\Laravel\post;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    TourCategory::factory()->create();
    TourDestination::factory()->create();
    $this->tour = Tour::factory()->create();
    $this->adminUser = User::factory()->create();
    \Illuminate\Support\Facades\Gate::define('tour-review-list', fn () => true);
    \Illuminate\Support\Facades\Gate::define('tour-review-view', fn () => true);
    \Illuminate\Support\Facades\Gate::define('tour-review-edit', fn () => true);
    \Illuminate\Support\Facades\Gate::define('tour-review-delete', fn () => true);
});

describe('Tour Review Index', function () {
    it('can view tour review index', function () {
        TourReview::factory()->count(3)->create(['tour_id' => $this->tour->id]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('tourReview.index'));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('TourReview/ReviewIndex')
                ->has('reviews.data', 3)
            );
    })->group('tour', 'tour-review');

    it('shows correct review data in index', function () {
        $review = TourReview::factory()->create([
            'tour_id' => $this->tour->id,
            'rating' => 4.5,
            'is_approved' => true,
            'is_featured' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('tourReview.index'));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('TourReview/ReviewIndex')
                ->has('reviews.data', 1)
                ->where('reviews.data.0.rating', '4.5')
                ->where('reviews.data.0.is_approved', true)
                ->where('reviews.data.0.is_featured', true)
            );
    })->group('tour', 'tour-review');
});

describe('Tour Review Show', function () {
    it('can view tour review details', function () {
        $review = TourReview::factory()->create([
            'tour_id' => $this->tour->id,
            'title' => 'Great Experience',
            'content' => 'This was an amazing tour!',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('tourReview.show', $review->id));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('TourReview/ReviewShow')
                ->has('review')
                ->where('review.title', 'Great Experience')
            );
    })->group('tour', 'tour-review');

    it('shows related tour information', function () {
        $review = TourReview::factory()->create(['tour_id' => $this->tour->id]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('tourReview.show', $review->id));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('review.tour')
            );
    })->group('tour', 'tour-review');
});

describe('Tour Review Approval', function () {
    it('can approve a pending review', function () {
        $review = TourReview::factory()->create([
            'tour_id' => $this->tour->id,
            'is_approved' => false,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->patch(route('tourReview.approve', $review->id));

        $response->assertRedirect();

        $review->refresh();
        expect($review->is_approved)->toBeTrue()
            ->and($review->approved_at)->not->toBeNull();
    })->group('tour', 'tour-review');

    it('can reject an approved review', function () {
        $review = TourReview::factory()->approved()->create([
            'tour_id' => $this->tour->id,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->patch(route('tourReview.reject', $review->id));

        $response->assertRedirect();

        $review->refresh();
        expect($review->is_approved)->toBeFalse()
            ->and($review->approved_at)->toBeNull();
    })->group('tour', 'tour-review');
});

describe('Tour Review Featured Toggle', function () {
    it('can toggle review featured status on', function () {
        $review = TourReview::factory()->create([
            'tour_id' => $this->tour->id,
            'is_featured' => false,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->patch(route('tourReview.toggleFeatured', $review->id));

        $response->assertRedirect();

        $review->refresh();
        expect($review->is_featured)->toBeTrue();
    })->group('tour', 'tour-review');

    it('can toggle review featured status off', function () {
        $review = TourReview::factory()->featured()->create([
            'tour_id' => $this->tour->id,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->patch(route('tourReview.toggleFeatured', $review->id));

        $response->assertRedirect();

        $review->refresh();
        expect($review->is_featured)->toBeFalse();
    })->group('tour', 'tour-review');
});

describe('Tour Review Delete', function () {
    it('can soft delete a review', function () {
        $review = TourReview::factory()->create(['tour_id' => $this->tour->id]);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('tourReview.destroy', $review->id));

        $response->assertRedirect(route('tourReview.index'));

        $this->assertSoftDeleted('tour_reviews', ['id' => $review->id]);
    })->group('tour', 'tour-review');
});

describe('Tour Review Recycle Bin', function () {
    it('can view recycle bin', function () {
        $review = TourReview::factory()->create(['tour_id' => $this->tour->id]);
        $review->delete();

        $response = $this->actingAs($this->adminUser)
            ->get(route('tourReview.recycleBin'));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('TourReview/ReviewRecycleBin')
                ->has('reviews.data', 1)
            );
    })->group('tour', 'tour-review', 'recycle-bin');

    it('can restore deleted review', function () {
        $review = TourReview::factory()->create(['tour_id' => $this->tour->id]);
        $review->delete();

        $response = $this->actingAs($this->adminUser)
            ->post(route('tourReview.restore', $review->id));

        $response->assertRedirect();

        $this->assertDatabaseHas('tour_reviews', [
            'id' => $review->id,
            'deleted_at' => null,
        ]);
    })->group('tour', 'tour-review', 'recycle-bin');

    it('can permanently delete review', function () {
        $review = TourReview::factory()->create(['tour_id' => $this->tour->id]);
        $review->delete();

        $response = $this->actingAs($this->adminUser)
            ->delete(route('tourReview.destroyForce', $review->id));

        $response->assertRedirect();

        $this->assertDatabaseMissing('tour_reviews', ['id' => $review->id]);
    })->group('tour', 'tour-review', 'recycle-bin');

    it('can empty recycle bin', function () {
        TourReview::factory()->count(3)->create(['tour_id' => $this->tour->id])
            ->each->delete();

        $response = $this->actingAs($this->adminUser)
            ->delete(route('tourReview.emptyRecycleBin'));

        $response->assertRedirect();

        expect(TourReview::onlyTrashed()->count())->toBe(0);
    })->group('tour', 'tour-review', 'recycle-bin');

    it('can restore all reviews', function () {
        TourReview::factory()->count(3)->create(['tour_id' => $this->tour->id])
            ->each->delete();

        $response = $this->actingAs($this->adminUser)
            ->post(route('tourReview.restoreRecycleBin'));

        $response->assertRedirect();

        expect(TourReview::onlyTrashed()->count())->toBe(0);
        expect(TourReview::count())->toBe(3);
    })->group('tour', 'tour-review', 'recycle-bin');
});

describe('Tour Review Authorization', function () {
    it('requires authentication to view reviews', function () {
        $response = get(route('tourReview.index'));

        $response->assertRedirect(route('auth.loginForm'));
    })->group('tour', 'tour-review', 'authorization');

    it('requires permission to view reviews', function () {
        $user = User::factory()->create();
        \Illuminate\Support\Facades\Gate::define('tour-review-list', fn () => false);

        $response = $this->actingAs($user)
            ->get(route('tourReview.index'));

        $response->assertForbidden();
    })->group('tour', 'tour-review', 'authorization');

    it('requires permission to approve reviews', function () {
        $user = User::factory()->create();
        \Illuminate\Support\Facades\Gate::define('tour-review-edit', fn () => false);

        $review = TourReview::factory()->create(['tour_id' => $this->tour->id]);

        $response = $this->actingAs($user)
            ->patch(route('tourReview.approve', $review->id));

        $response->assertForbidden();
    })->group('tour', 'tour-review', 'authorization');

    it('requires permission to delete reviews', function () {
        $user = User::factory()->create();
        \Illuminate\Support\Facades\Gate::define('tour-review-delete', fn () => false);

        $review = TourReview::factory()->create(['tour_id' => $this->tour->id]);

        $response = $this->actingAs($user)
            ->delete(route('tourReview.destroy', $review->id));

        $response->assertForbidden();
    })->group('tour', 'tour-review', 'authorization');
});
