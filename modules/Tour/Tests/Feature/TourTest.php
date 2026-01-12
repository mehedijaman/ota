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

describe('Tour Recycle Bin', function () {
    beforeEach(function () {
        $this->user = \App\Models\User::factory()->create();
        \Illuminate\Support\Facades\Gate::define('tour-delete', fn () => true);
        $this->actingAs($this->user);
        TourCategory::factory()->create();
        TourDestination::factory()->create();
    });

    it('can view recycle bin', function () {
        $tour = Tour::factory()->create();
        $tour->delete();

        $response = get(route('tour.recycleBin'));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Tour/TourRecycleBin')
                ->has('tours.data', 1)
            );
    })->group('tour', 'recycle-bin');

    it('can restore deleted tour', function () {
        $tour = Tour::factory()->create();
        $tour->delete();

        $response = post(route('tour.restore', $tour->id));

        $response->assertRedirect(route('tour.recycleBin'));

        $this->assertDatabaseHas('tours', [
            'id' => $tour->id,
            'deleted_at' => null,
        ]);
    })->group('tour', 'recycle-bin');

    it('can permanently delete tour', function () {
        $tour = Tour::factory()->create();
        $tour->delete();

        $response = delete(route('tour.destroyForce', $tour->id));

        $response->assertRedirect(route('tour.recycleBin'));

        $this->assertDatabaseMissing('tours', ['id' => $tour->id]);
    })->group('tour', 'recycle-bin');

    it('can empty recycle bin', function () {
        Tour::factory()->count(3)->create()->each->delete();

        $response = delete(route('tour.emptyRecycleBin'));

        $response->assertRedirect(route('tour.recycleBin'));

        expect(Tour::onlyTrashed()->count())->toBe(0);
    })->group('tour', 'recycle-bin');

    it('can restore all tours', function () {
        Tour::factory()->count(3)->create()->each->delete();

        $response = post(route('tour.restoreRecycleBin'));

        $response->assertRedirect(route('tour.recycleBin'));

        expect(Tour::onlyTrashed()->count())->toBe(0);
        expect(Tour::count())->toBe(3);
    })->group('tour', 'recycle-bin');
});

describe('Tour Validation', function () {
    beforeEach(function () {
        $this->user = \App\Models\User::factory()->create();
        \Illuminate\Support\Facades\Gate::define('tour-create', fn () => true);
        $this->actingAs($this->user);
        TourCategory::factory()->create();
        TourDestination::factory()->create();
    });

    it('requires name when creating tour', function () {
        $response = post(route('tour.store'), [
            'slug' => 'test-slug',
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors('name');
    })->group('tour', 'validation');

    it('requires unique slug', function () {
        Tour::factory()->create(['slug' => 'existing-slug']);

        $response = post(route('tour.store'), [
            'name' => 'New Tour',
            'slug' => 'existing-slug',
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors('slug');
    })->group('tour', 'validation');

    it('validates duration days is positive', function () {
        $response = post(route('tour.store'), [
            'name' => 'Test Tour',
            'slug' => 'test-tour',
            'duration_days' => -1,
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors('duration_days');
    })->group('tour', 'validation');
});
