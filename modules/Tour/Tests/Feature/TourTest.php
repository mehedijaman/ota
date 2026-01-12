<?php

use Modules\Tour\Models\Tour;
use Modules\Tour\Models\TourCategory;
use Modules\Tour\Models\TourDestination;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

uses(\Tests\TestCase::class, \Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->user = \App\Models\User::factory()->create();
    \Illuminate\Support\Facades\Gate::define('tour-list', fn () => true);
    $this->actingAs($this->user);
    TourCategory::factory()->create();
    TourDestination::factory()->create();
});

it('can view tour index', function () {
    Tour::factory()->count(3)->create();

    $response = get(route('tour.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Tour/TourIndex')
            ->has('tours.data', 3)
        );
})->group('tour');

it('can view create tour form', function () {
    $this->user = \App\Models\User::factory()->create();
    \Illuminate\Support\Facades\Gate::define('tour-create', fn () => true);
    $this->actingAs($this->user);

    $response = get(route('tour.create'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Tour/TourForm')
            ->has('categories')
            ->has('destinations')
        );
})->group('tour');

it('can create tour', function () {
    $this->user = \App\Models\User::factory()->create();
    \Illuminate\Support\Facades\Gate::define('tour-create', fn () => true);
    $this->actingAs($this->user);

    $category = TourCategory::first();
    $destination = TourDestination::first();

    $tourData = [
        'name' => 'Test Beach Tour',
        'slug' => 'test-beach-tour',
        'category_id' => $category->id,
        'destination_id' => $destination->id,
        'duration_days' => 3,
        'duration_nights' => 2,
        'difficulty_level' => 'easy',
        'tour_type' => 'group',
        'base_price' => 5000,
        'is_active' => true,
    ];

    $response = post(route('tour.store'), $tourData);

    $response->assertRedirect(route('tour.index'));

    assertDatabaseHas('tours', [
        'name' => 'Test Beach Tour',
        'slug' => 'test-beach-tour',
    ]);
})->group('tour');

it('can view edit tour form', function () {
    $this->user = \App\Models\User::factory()->create();
    \Illuminate\Support\Facades\Gate::define('tour-edit', fn () => true);
    $this->actingAs($this->user);

    $tour = Tour::factory()->create();

    $response = get(route('tour.edit', $tour->id));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Tour/TourForm')
            ->has('tour')
            ->has('categories')
            ->has('destinations')
        );
})->group('tour');

it('can update tour', function () {
    $this->user = \App\Models\User::factory()->create();
    \Illuminate\Support\Facades\Gate::define('tour-edit', fn () => true);
    $this->actingAs($this->user);

    $tour = Tour::factory()->create();

    $response = put(route('tour.update', $tour->id), [
        'name' => 'Updated Tour Name',
        'slug' => 'updated-tour-name',
        'duration_days' => 5,
        'duration_nights' => 4,
        'difficulty_level' => 'moderate',
        'tour_type' => 'private',
        'base_price' => 8000,
        'is_active' => true,
    ]);

    $response->assertRedirect(route('tour.index'));

    assertDatabaseHas('tours', [
        'id' => $tour->id,
        'name' => 'Updated Tour Name',
    ]);
})->group('tour');

it('can delete tour', function () {
    $this->user = \App\Models\User::factory()->create();
    \Illuminate\Support\Facades\Gate::define('tour-delete', fn () => true);
    $this->actingAs($this->user);

    $tour = Tour::factory()->create();

    $response = delete(route('tour.destroy', $tour->id));

    $response->assertRedirect(route('tour.index'));

    $this->assertSoftDeleted('tours', ['id' => $tour->id]);
})->group('tour');

it('can view tour details', function () {
    $this->user = \App\Models\User::factory()->create();
    \Illuminate\Support\Facades\Gate::define('tour-view', fn () => true);
    $this->actingAs($this->user);

    $tour = Tour::factory()->create();

    $response = get(route('tour.show', $tour->id));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Tour/TourShow')
            ->has('tour')
        );
})->group('tour');
