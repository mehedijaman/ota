<?php

use Modules\Tour\Models\TourCategory;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

uses(\Tests\TestCase::class, \Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->user = \App\Models\User::factory()->create();
    \Illuminate\Support\Facades\Gate::define('tour-category-list', fn() => true);
    $this->actingAs($this->user);
});

it('can view tour category index', function () {
    TourCategory::factory()->count(3)->create();

    $response = get(route('tourCategory.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('TourCategory/CategoryIndex')
            ->has('categories.data', 3)
        );
})->group('tour', 'tour-category');

it('can view create tour category form', function () {
    $this->user = \App\Models\User::factory()->create();
    \Illuminate\Support\Facades\Gate::define('tour-category-create', fn() => true);
    $this->actingAs($this->user);

    $response = get(route('tourCategory.create'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('TourCategory/CategoryForm')
        );
})->group('tour', 'tour-category');

it('can create tour category', function () {
    $this->user = \App\Models\User::factory()->create();
    \Illuminate\Support\Facades\Gate::define('tour-category-create', fn() => true);
    $this->actingAs($this->user);

    $categoryData = [
        'name' => 'Beach Tours',
        'slug' => 'beach-tours',
        'is_active' => true,
    ];

    $response = post(route('tourCategory.store'), $categoryData);

    $response->assertRedirect(route('tourCategory.index'));

    assertDatabaseHas('tour_categories', [
        'name' => 'Beach Tours',
        'slug' => 'beach-tours',
    ]);
})->group('tour', 'tour-category');

it('can view edit tour category form', function () {
    $this->user = \App\Models\User::factory()->create();
    \Illuminate\Support\Facades\Gate::define('tour-category-edit', fn() => true);
    $this->actingAs($this->user);

    $category = TourCategory::factory()->create();

    $response = get(route('tourCategory.edit', $category->id));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('TourCategory/CategoryForm')
            ->has('category')
        );
})->group('tour', 'tour-category');

it('can update tour category', function () {
    $this->user = \App\Models\User::factory()->create();
    \Illuminate\Support\Facades\Gate::define('tour-category-edit', fn() => true);
    $this->actingAs($this->user);

    $category = TourCategory::factory()->create();

    $response = put(route('tourCategory.update', $category->id), [
        'name' => 'Updated Category',
        'slug' => 'updated-category',
        'is_active' => true,
    ]);

    $response->assertRedirect(route('tourCategory.index'));

    assertDatabaseHas('tour_categories', [
        'id' => $category->id,
        'name' => 'Updated Category',
    ]);
})->group('tour', 'tour-category');

it('can delete tour category', function () {
    $this->user = \App\Models\User::factory()->create();
    \Illuminate\Support\Facades\Gate::define('tour-category-delete', fn() => true);
    $this->actingAs($this->user);

    $category = TourCategory::factory()->create();

    $response = delete(route('tourCategory.destroy', $category->id));

    $response->assertRedirect(route('tourCategory.index'));

    $this->assertSoftDeleted('tour_categories', ['id' => $category->id]);
})->group('tour', 'tour-category');
