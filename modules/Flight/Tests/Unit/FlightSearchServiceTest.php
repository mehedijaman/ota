<?php

use Modules\Flight\DTOs\FlightSearchRequest;
use Modules\Flight\DTOs\FlightSearchResult;
use Modules\Flight\Models\FlightAirport;
use Modules\Flight\Services\FlightSearchService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->service = new FlightSearchService;
});

describe('FlightSearchService', function () {
    it('can create search request from array', function () {
        $data = [
            'trip_type' => 'round_trip',
            'origin' => 'dac',
            'destination' => 'dxb',
            'departure_date' => '2026-02-01',
            'return_date' => '2026-02-08',
            'adults' => 2,
            'children' => 1,
            'cabin_class' => 'business',
        ];

        $request = FlightSearchRequest::fromArray($data);

        expect($request->tripType)->toBe('round_trip');
        expect($request->originCode)->toBe('DAC');
        expect($request->destinationCode)->toBe('DXB');
        expect($request->adults)->toBe(2);
        expect($request->children)->toBe(1);
        expect($request->cabinClass)->toBe('business');
        expect($request->isRoundTrip())->toBeTrue();
    });

    it('can get total passengers', function () {
        $request = FlightSearchRequest::fromArray([
            'trip_type' => 'one_way',
            'origin' => 'DAC',
            'destination' => 'DXB',
            'departure_date' => '2026-02-01',
            'adults' => 2,
            'children' => 1,
            'infants' => 1,
        ]);

        expect($request->getTotalPassengers())->toBe(4);
    });

    it('can generate cache key', function () {
        $request1 = FlightSearchRequest::fromArray([
            'trip_type' => 'one_way',
            'origin' => 'DAC',
            'destination' => 'DXB',
            'departure_date' => '2026-02-01',
            'adults' => 1,
        ]);

        $request2 = FlightSearchRequest::fromArray([
            'trip_type' => 'one_way',
            'origin' => 'DAC',
            'destination' => 'DXB',
            'departure_date' => '2026-02-01',
            'adults' => 1,
        ]);

        $request3 = FlightSearchRequest::fromArray([
            'trip_type' => 'one_way',
            'origin' => 'DAC',
            'destination' => 'LHR', // Different
            'departure_date' => '2026-02-01',
            'adults' => 1,
        ]);

        expect($request1->getCacheKey())->toBe($request2->getCacheKey());
        expect($request1->getCacheKey())->not->toBe($request3->getCacheKey());
    });

    it('can search airports', function () {
        FlightAirport::factory()->create([
            'iata_code' => 'DAC',
            'name' => 'Hazrat Shahjalal International Airport',
            'city' => 'Dhaka',
            'is_active' => true,
            'is_popular' => true,
        ]);

        FlightAirport::factory()->create([
            'iata_code' => 'CGP',
            'name' => 'Shah Amanat International Airport',
            'city' => 'Chittagong',
            'is_active' => true,
        ]);

        $results = $this->service->searchAirports('Dhaka');

        expect($results)->toHaveCount(1);
        expect($results->first()->iata_code)->toBe('DAC');
    });

    it('filters results by direct only', function () {
        $request = FlightSearchRequest::fromArray([
            'trip_type' => 'one_way',
            'origin' => 'DAC',
            'destination' => 'DXB',
            'departure_date' => now()->addDays(7)->toDateString(),
            'adults' => 1,
            'direct_only' => true,
        ]);

        $results = collect([
            FlightSearchResult::fromArray(['stops' => 0, 'total_price' => 100]),
            FlightSearchResult::fromArray(['stops' => 1, 'total_price' => 80]),
            FlightSearchResult::fromArray(['stops' => 0, 'total_price' => 120]),
        ]);

        $filtered = $this->service->filterResults($results, $request);

        expect($filtered)->toHaveCount(2);
        expect($filtered->every(fn ($r) => $r->stops === 0))->toBeTrue();
    });

    it('sorts results by price', function () {
        $results = collect([
            FlightSearchResult::fromArray(['total_price' => 300]),
            FlightSearchResult::fromArray(['total_price' => 100]),
            FlightSearchResult::fromArray(['total_price' => 200]),
        ]);

        $sorted = $this->service->sortResults($results, 'price', 'asc');

        expect($sorted->values()->pluck('totalPrice')->toArray())->toBe([100.0, 200.0, 300.0]);
    });
});
