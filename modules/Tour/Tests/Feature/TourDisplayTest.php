<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Tour\Models\Tour;
use Modules\Tour\Models\TourCategory;
use Modules\Tour\Models\TourDestination;
use Modules\Tour\Models\TourPricing;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->category = TourCategory::factory()->create(['name' => 'Adventure']);
    $this->destination = TourDestination::factory()->create(['name' => 'Sundarbans']);
});

describe('Tour Listing', function () {
    it('displays tour index page', function () {
        Tour::factory()->count(5)->create(['is_active' => true]);

        $response = $this->get(route('site.tour.index'));

        $response->assertOk()
            ->assertSee('Tours');
    });

    it('shows only active tours', function () {
        $activeTour = Tour::factory()->create([
            'name' => 'Active Tour',
            'is_active' => true,
        ]);

        $inactiveTour = Tour::factory()->create([
            'name' => 'Inactive Tour',
            'is_active' => false,
        ]);

        $response = $this->get(route('site.tour.search'));

        $response->assertSee('Active Tour')
            ->assertDontSee('Inactive Tour');
    });

    it('can filter tours by category', function () {
        $adventureTour = Tour::factory()->create([
            'name' => 'Adventure Tour',
            'category_id' => $this->category->id,
            'is_active' => true,
        ]);

        $otherCategory = TourCategory::factory()->create(['name' => 'Cultural']);
        $culturalTour = Tour::factory()->create([
            'name' => 'Cultural Tour',
            'category_id' => $otherCategory->id,
            'is_active' => true,
        ]);

        $response = $this->get(route('site.tour.search', ['category' => $this->category->id]));

        $response->assertSee('Adventure Tour')
            ->assertDontSee('Cultural Tour');
    });

    it('can filter tours by destination', function () {
        $sundarbansTour = Tour::factory()->create([
            'name' => 'Sundarbans Tour',
            'destination_id' => $this->destination->id,
            'is_active' => true,
        ]);

        $otherDestination = TourDestination::factory()->create(['name' => 'Cox\'s Bazar']);
        $coxsBazarTour = Tour::factory()->create([
            'name' => 'Cox\'s Bazar Tour',
            'destination_id' => $otherDestination->id,
            'is_active' => true,
        ]);

        $response = $this->get(route('site.tour.search', ['destination' => $this->destination->id]));

        $response->assertSee('Sundarbans Tour')
            ->assertDontSee('Cox\'s Bazar Tour');
    });

    it('can search tours by name', function () {
        Tour::factory()->create([
            'name' => 'Sundarbans Adventure',
            'is_active' => true,
        ]);

        Tour::factory()->create([
            'name' => 'Beach Paradise',
            'is_active' => true,
        ]);

        $response = $this->get(route('site.tour.search', ['q' => 'Sundarbans']));

        $response->assertSee('Sundarbans Adventure')
            ->assertDontSee('Beach Paradise');
    });
});

describe('Tour Detail Page', function () {
    it('displays tour detail page', function () {
        $tour = Tour::factory()->create([
            'name' => 'Amazing Tour',
            'slug' => 'amazing-tour',
            'description' => 'This is an amazing tour',
            'is_active' => true,
        ]);

        TourPricing::factory()->create([
            'tour_id' => $tour->id,
            'name' => 'Standard Package',
            'adult_price' => 5000,
            'is_active' => true,
        ]);

        TourPricing::factory()->create([
            'tour_id' => $tour->id,
            'name' => 'Deluxe Package',
            'adult_price' => 8000,
            'is_active' => true,
        ]);

        $response = $this->get(route('site.tour.show', $tour->slug));

        $response->assertOk()
            ->assertSee('Amazing Tour')
            ->assertSee('This is an amazing tour')
            ->assertSee('Standard Package')
            ->assertSee('5,000');
    });

    it('shows 404 for inactive tour', function () {
        $tour = Tour::factory()->create([
            'slug' => 'inactive-tour',
            'is_active' => false,
        ]);

        $response = $this->get(route('site.tour.show', $tour->slug));

        $response->assertNotFound();
    });

    it('shows 404 for non-existent tour', function () {
        $response = $this->get(route('site.tour.show', 'non-existent-tour'));

        $response->assertNotFound();
    });

    it('displays tour pricings', function () {
        $tour = Tour::factory()->create(['is_active' => true]);

        TourPricing::factory()->create([
            'tour_id' => $tour->id,
            'name' => 'Budget Package',
            'adult_price' => 3000,
        ]);

        TourPricing::factory()->create([
            'tour_id' => $tour->id,
            'name' => 'Premium Package',
            'adult_price' => 8000,
        ]);

        $response = $this->get(route('site.tour.show', $tour->slug));

        $response->assertSee('Budget Package')
            ->assertSee('Premium Package')
            ->assertSee('3,000')
            ->assertSee('8,000');
    });

    it('displays tour extras', function () {
        $tour = Tour::factory()->create(['is_active' => true]);

        $tour->extras()->create([
            'name' => 'Airport Transfer',
            'description' => 'Pick up and drop off',
            'price' => 1000,
        ]);

        $tour->extras()->create([
            'name' => 'Travel Insurance',
            'description' => 'Full coverage',
            'price' => 500,
        ]);

        $response = $this->get(route('site.tour.show', $tour->slug));

        $response->assertSee('Airport Transfer')
            ->assertSee('Travel Insurance')
            ->assertSee('1,000')
            ->assertSee('500');
    });

    it('shows book now button for authenticated users', function () {
        $user = User::factory()->create();
        $tour = Tour::factory()->create(['is_active' => true]);

        $response = $this->actingAs($user)
            ->get(route('site.tour.show', $tour->slug));

        $response->assertSee('Book Now');
    });

    it('shows login prompt for guests', function () {
        $tour = Tour::factory()->create(['is_active' => true]);

        $response = $this->get(route('site.tour.show', $tour->slug));

        $response->assertSee('Login');
    });
});

describe('Tour Reviews', function () {
    it('can submit a review for authenticated users', function () {
        $user = User::factory()->create();
        $tour = Tour::factory()->create(['is_active' => true]);

        $reviewData = [
            'rating' => 5,
            'comment' => 'Amazing experience!',
            'reviewer_name' => $user->name,
            'reviewer_email' => $user->email,
        ];

        $response = $this->actingAs($user)
            ->post(route('site.tour.submitReview', $tour->slug), $reviewData);

        $response->assertRedirect();

        $this->assertDatabaseHas('tour_reviews', [
            'tour_id' => $tour->id,
            'user_id' => $user->id,
            'rating' => 5,
            'content' => 'Amazing experience!',
            'name' => $user->name,
            'email' => $user->email,
        ]);
    });

    it('requires authentication to submit review', function () {
        $tour = Tour::factory()->create(['is_active' => true]);

        $response = $this->post(route('site.tour.submitReview', $tour->slug), [
            'rating' => 5,
            'comment' => 'Great tour!',
        ]);

        $response->assertRedirect(route('auth.loginForm'));
    });

    it('validates review rating', function () {
        $user = User::factory()->create();
        $tour = Tour::factory()->create(['is_active' => true]);

        $response = $this->actingAs($user)
            ->post(route('site.tour.submitReview', $tour->slug), [
                'rating' => 6, // Invalid rating
                'comment' => 'Test',
                'reviewer_name' => $user->name,
                'reviewer_email' => $user->email,
            ]);

        $response->assertSessionHasErrors('rating');
    });

    it('displays reviews on tour page', function () {
        $user = User::factory()->create(['name' => 'John Doe']);
        $tour = Tour::factory()->create(['is_active' => true]);

        $tour->reviews()->create([
            'user_id' => $user->id,
            'rating' => 5,
            'content' => 'Excellent tour!',
            'name' => 'John Doe',
            'is_approved' => true,
        ]);

        $response = $this->get(route('site.tour.show', $tour->slug));

        $response->assertSee('John Doe')
            ->assertSee('Excellent tour!');
    });
});
