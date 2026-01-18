<?php

use App\Models\User;
use Modules\Flight\Models\FlightAirline;
use Modules\Flight\Models\FlightAirport;
use Modules\Flight\Models\FlightBooking;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    \Illuminate\Support\Facades\Mail::fake();
    
    // Create permissions
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    $permissions = [
        'flight.booking.view',
        'flight.booking.create',
        'flight.booking.edit',
        'flight.booking.delete',
        'flight.payment.create',
    ];
    foreach ($permissions as $permission) {
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'user']);
    }

    $this->user = User::factory()->create();
    $this->user->givePermissionTo($permissions);
});

describe('FlightBookingController', function () {
    it('can list bookings', function () {
        FlightBooking::factory()->count(3)->create();

        $response = actingAs($this->user)->get(route('flightBooking.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Flight/Bookings/Index')
            ->has('bookings.data', 3)
        );
    });

    it('can show booking create form', function () {
        $response = actingAs($this->user)->get(route('flightBooking.create'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('Flight/Bookings/Form'));
    });

    it('can create a booking', function () {
        $airport1 = FlightAirport::factory()->create();
        $airport2 = FlightAirport::factory()->create();
        $airline = FlightAirline::factory()->create();

        $data = [
            'trip_type' => 'one_way',
            'departure_date' => now()->addDays(7)->toDateString(),
            'adult_count' => 2,
            'child_count' => 0,
            'infant_count' => 0,
            'contact_title' => 'Mr',
            'contact_first_name' => 'John',
            'contact_last_name' => 'Doe',
            'contact_email' => 'john@example.com',
            'contact_phone_country_code' => '+880',
            'contact_phone' => '1712345678',
            'segments' => [
                [
                    'segment_order' => 1,
                    'segment_type' => 'outbound',
                    'airline_id' => $airline->id,
                    'flight_number' => '123',
                    'departure_airport_id' => $airport1->id,
                    'arrival_airport_id' => $airport2->id,
                    'departure_datetime' => now()->addDays(7)->toDateTimeString(),
                    'arrival_datetime' => now()->addDays(7)->addHours(3)->toDateTimeString(),
                    'cabin_class' => 'economy',
                ],
            ],
            'passengers' => [
                [
                    'passenger_type' => 'adult',
                    'title' => 'Mr',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'gender' => 'male',
                    'date_of_birth' => '1990-01-01',
                    'is_lead_passenger' => true,
                ],
                [
                    'passenger_type' => 'adult',
                    'title' => 'Mrs',
                    'first_name' => 'Jane',
                    'last_name' => 'Doe',
                    'gender' => 'female',
                    'date_of_birth' => '1992-05-15',
                ],
            ],
        ];

        $response = actingAs($this->user)->post(route('flightBooking.store'), $data);

        $response->assertRedirect();
        assertDatabaseHas('flight_bookings', [
            'contact_email' => 'john@example.com',
            'adult_count' => 2,
        ]);
    });

    it('can show a booking', function () {
        $booking = FlightBooking::factory()->create();

        $response = actingAs($this->user)->get(route('flightBooking.show', $booking->id));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Flight/Bookings/Show')
            ->has('booking')
        );
    });

    it('can update booking status', function () {
        $booking = FlightBooking::factory()->create(['status' => 'pending']);

        $response = actingAs($this->user)->patch(route('flightBooking.updateStatus', [
            'id' => $booking->id,
            'status' => 'confirmed',
        ]));

        $response->assertRedirect();

        $booking->refresh();
        expect($booking->status)->toBe('confirmed');
    });

    it('can delete a booking', function () {
        $booking = FlightBooking::factory()->create();

        $response = actingAs($this->user)->delete(route('flightBooking.destroy', $booking->id));

        $response->assertRedirect();
        expect(FlightBooking::find($booking->id))->toBeNull();
    });
});
