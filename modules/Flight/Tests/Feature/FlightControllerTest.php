<?php

namespace Modules\Flight\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Flight\Models\FlightAirline;
use Modules\Flight\Models\FlightAirport;
use Tests\TestCase;

class FlightControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        // Assuming permission handling or super admin role.
        // For simplicity in this generated test suite, we assume the user has access
        // or we mock the permission check if it's middleware based.
        // If Spatie permissions:
        // $role = Role::create(['name' => 'admin']);
        // $this->admin->assignRole($role);
        // OR Gate::define...
        
        // Since I can't easily set up full permissions in this environment without knowing the exact permission system details (likely Spatie or basic Gates),
        // I will assume the factory user might need a role or checking `can` middleware.
        // However, I see `->can('flight.view')` in routes.
        // I will verify if I can bypass permissions or if I should assign them.
        // I'll assume standard Laravel `actingAs`.
    }
    
    // Helper to bypass permission middleware if possible or just rely on actingAs if permissions are seeded
    // For now I'll adding a basic test that asserts redirects (403) if not authorized, 
    // or success if I can mock permission.
    
    // I'll try to mock the `can` middleware behavior by granting permissions?
    // User::factory()->create() usually lacks permissions.
    // I will use `withoutMiddleware` for simple route existence check, OR better:
    // mock permissions.
    
    protected function authenticate()
    {
         $this->actingAs($this->admin);
         // If using Spatie:
         // $this->admin->givePermissionTo(['flight.view', 'flight.airport.view', 'flight.airport.create', ...]);
         // Since I don't want to depend on unrelated modules/seeders, I might skip permission setup and just check if route exists (which returns 403, meaning route is hit).
         // BUT 403 means controller logic wasn't reached.
         // Best is `withoutMiddleware()` to test controller logic.
    }

    public function test_can_view_flight_dashboard()
    {
        $this->authenticate();
        $this->withoutMiddleware(); // Bypass usage of 'can' middleware for unit testing controller logic

        $response = $this->get(route('flight.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('FlightMasterData/FlightMasterDataIndex')
        );
    }

    public function test_can_view_airport_list()
    {
        $this->authenticate();
        $this->withoutMiddleware();

        FlightAirport::factory()->count(3)->create();

        $response = $this->get(route('flight.airport.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('FlightAirport/FlightAirportIndex')
            ->has('airports.data', 3)
        );
    }

    public function test_can_create_airport()
    {
        $this->authenticate();
        $this->withoutMiddleware();

        $country = \Nnjeim\World\Models\Country::create([
            'name' => 'Test Country',
            'iso2' => 'TC',
            'iso3' => 'TST',
            'phone_code' => '123',
            'region' => 'Asia',
            'subregion' => 'Southern Asia',
        ]);

        $airportData = [
            'name' => 'International Test Airport',
            'iata_code' => 'TST',
            'city' => 'Test City',
            'country_id' => $country->id,
            'type' => 'large_airport',
            'is_international' => true,
            'is_active' => true,
            'latitude' => 23.8103,
            'longitude' => 90.4125,
            'timezone' => 'Asia/Dhaka',
        ];

        $response = $this->post(route('flight.airport.store'), $airportData);

        $response->assertRedirect(route('flight.airport.index'));
        $this->assertDatabaseHas('flight_airports', ['iata_code' => 'TST']);
    }

    public function test_can_view_airline_list()
    {
        $this->authenticate();
        $this->withoutMiddleware();

        FlightAirline::factory()->count(3)->create();

        $response = $this->get(route('flight.airline.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('FlightAirline/FlightAirlineIndex')
            ->has('airlines.data', 3)
        );
    }
}
