<?php

namespace Modules\Flight\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Flight\Models\FlightAirport;
use Tests\TestCase;

class SiteFlightControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        if (! \Illuminate\Support\Facades\Route::has('login')) {
             \Illuminate\Support\Facades\Route::get('login', fn() => 'login')->name('login');
        }
    }

    public function test_can_view_public_flight_search_page()
    {
        // Public route name renamed to 'flight.home' to avoid Admin conflict
        $response = $this->get(route('flight.home'));

        $response->assertStatus(200);
        $response->assertViewIs('flight::search');
    }

    public function test_can_view_flight_search_results()
    {
        // Mocking Adapter via mocking configuration or container binding might be needed if SearchService calls Adapter.
        // Or assume Mock Mode is configured in Setup.
        // We will seed supplier with mock_mode = true.
        
        \Modules\Flight\Models\FlightSupplier::factory()->create([
            'code' => 'amadeus',
            'is_active' => true,
            'api_settings' => ['mock_mode' => true]
        ]);

        $params = [
            'origin' => 'DAC',
            'destination' => 'CXB',
            'departure_date' => now()->addDays(5)->format('Y-m-d'),
            'adults' => 1,
        ];

        $response = $this->get(route('flight.results', $params));

        $response->assertStatus(200);
        $response->assertViewIs('flight::results');
        $response->assertViewHas('results');
    }

    public function test_booking_page_requires_auth()
    {
        if (! \Illuminate\Support\Facades\Route::has('login')) {
             \Illuminate\Support\Facades\Route::get('login', fn() => 'login')->name('login');
        }

        $response = $this->get(route('flight.booking', ['offer_id' => 'test']));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_booking_page()
    {
        $user = \App\Models\User::factory()->create();
        
        $response = $this->actingAs($user)->get(route('flight.booking', ['offer_id' => 'test']));
        
        $response->assertStatus(200);
        $response->assertViewIs('flight::booking');
    }
}
