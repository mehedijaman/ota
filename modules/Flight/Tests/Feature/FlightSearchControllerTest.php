<?php

use App\Models\User;
use Modules\Flight\Models\FlightAirport;

use function Pest\Laravel\actingAs;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    // Create permission first
    // Guard might be web or api. User model usually uses web.
    // If error says 'user' guard, we might need to specify it or check auth config.
    // Typically Spatie requires permission to exist.
    // We'll create it for 'web' and 'api' to be safe, or just relying on actingAs bypassing checks if we used withoutMiddleware, but here we test permissions?
    // Actually the test didn't fail on 403, it failed on PermissionDoesNotExist.
    
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'flight.view', 'guard_name' => 'user']);
    // If the app uses a custom guard 'user' (highly unlikely unless configured), we add it. 
    // But error 'guard user' might imply $user->guard_name property?
    
    $this->user = User::factory()->create();
    $this->user->givePermissionTo('flight.view');
});

describe('FlightSearchController', function () {
    it('can search flights', function () {
        FlightAirport::factory()->create(['iata_code' => 'DAC']);
        FlightAirport::factory()->create(['iata_code' => 'DXB']);

        $response = actingAs($this->user)->postJson(route('flight.api.search'), [
            'trip_type' => 'one_way',
            'origin' => 'DAC',
            'destination' => 'DXB',
            'departure_date' => now()->addDays(7)->toDateString(),
            'adults' => 1,
            'cabin_class' => 'economy',
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'data' => [
                'results',
                'count',
                'search',
            ],
        ]);
    });

    it('validates search parameters', function () {
        $response = actingAs($this->user)->postJson(route('flight.api.search'), [
            'trip_type' => 'one_way',
            // Missing required fields
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['origin', 'destination', 'departure_date']);
    });

    it('validates origin and destination are different', function () {
        $response = actingAs($this->user)->postJson(route('flight.api.search'), [
            'trip_type' => 'one_way',
            'origin' => 'DAC',
            'destination' => 'DAC',
            'departure_date' => now()->addDays(7)->toDateString(),
            'adults' => 1,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['origin']);
    });

    it('validates return date for round trip', function () {
        $response = actingAs($this->user)->postJson(route('flight.api.search'), [
            'trip_type' => 'round_trip',
            'origin' => 'DAC',
            'destination' => 'DXB',
            'departure_date' => now()->addDays(7)->toDateString(),
            'adults' => 1,
            // Missing return_date
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['return_date']);
    });

    it('can search airports for autocomplete', function () {
        FlightAirport::factory()->create([
            'iata_code' => 'DAC',
            'name' => 'Hazrat Shahjalal International Airport',
            'city' => 'Dhaka',
            'is_active' => true,
        ]);

        $response = actingAs($this->user)->get(route('flight.api.airports').'?q=DAC');

        $response->assertOk();
        $response->assertJsonCount(1);
    });
});
