<?php

use Modules\Tour\Models\TourDestination;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

beforeEach(function () {
    $this->user = createUserWithPermission('tour-destination-list');
});

it('can view tour destination index', function () {
    TourDestination::factory()->count(3)->create();

    $response = get(route('tourDestination.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('TourDestination/DestinationIndex')
            ->has('destinations.data', 3)
        );
})->group('tour', 'tour-destination');

it('can view create tour destination form', function () {
    $this->user = createUserWithPermission('tour-destination-create');

    $response = get(route('tourDestination.create'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('TourDestination/DestinationForm')
        );
})->group('tour', 'tour-destination');

it('can create tour destination', function () {
    $this->user = createUserWithPermission('tour-destination-create');

    $destinationData = [
        'name' => "Cox's Bazar",
        'slug' => 'coxs-bazar',
        'country' => 'Bangladesh',
        'city' => "Cox's Bazar",
        'is_active' => true,
    ];

    $response = post(route('tourDestination.store'), $destinationData);

    $response->assertRedirect(route('tourDestination.index'));

    assertDatabaseHas('tour_destinations', [
        'name' => "Cox's Bazar",
        'slug' => 'coxs-bazar',
    ]);
})->group('tour', 'tour-destination');

it('can view edit tour destination form', function () {
    $this->user = createUserWithPermission('tour-destination-edit');

    $destination = TourDestination::factory()->create();

    $response = get(route('tourDestination.edit', $destination->id));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('TourDestination/DestinationForm')
            ->has('destination')
        );
})->group('tour', 'tour-destination');

it('can update tour destination', function () {
    $this->user = createUserWithPermission('tour-destination-edit');

    $destination = TourDestination::factory()->create();

    $response = put(route('tourDestination.update', $destination->id), [
        'name' => 'Updated Destination',
        'slug' => 'updated-destination',
        'is_active' => true,
    ]);

    $response->assertRedirect(route('tourDestination.index'));

    assertDatabaseHas('tour_destinations', [
        'id' => $destination->id,
        'name' => 'Updated Destination',
    ]);
})->group('tour', 'tour-destination');

it('can delete tour destination', function () {
    $this->user = createUserWithPermission('tour-destination-delete');

    $destination = TourDestination::factory()->create();

    $response = delete(route('tourDestination.destroy', $destination->id));

    $response->assertRedirect(route('tourDestination.index'));

    $this->assertDatabaseMissing('tour_destinations', ['id' => $destination->id]);
})->group('tour', 'tour-destination');
