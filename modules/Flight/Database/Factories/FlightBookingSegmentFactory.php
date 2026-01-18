<?php

namespace Modules\Flight\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Flight\Models\Flight;
use Modules\Flight\Models\FlightAircraftType;
use Modules\Flight\Models\FlightAirline;
use Modules\Flight\Models\FlightAirport;
use Modules\Flight\Models\FlightBooking;
use Modules\Flight\Models\FlightBookingSegment;
use Modules\Flight\Models\FlightFare;

class FlightBookingSegmentFactory extends Factory
{
    protected $model = FlightBookingSegment::class;

    public function definition(): array
    {
        $departureTime = $this->faker->dateTimeBetween('+1 week', '+3 months');
        $durationMinutes = $this->faker->numberBetween(60, 480);

        return [
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
            'booking_id' => FlightBooking::factory(),
            'flight_id' => Flight::inRandomOrder()->first()?->id ?? Flight::factory()->create()->id,
            'fare_id' => FlightFare::inRandomOrder()->first()?->id,
            'segment_order' => 1,
            'segment_type' => 'outbound',
            'airline_id' => FlightAirline::inRandomOrder()->first()?->id ?? FlightAirline::factory()->create()->id,
            'flight_number' => $this->faker->numberBetween(100, 9999),
            'departure_airport_id' => FlightAirport::inRandomOrder()->first()?->id ?? FlightAirport::factory()->create()->id,
            'arrival_airport_id' => FlightAirport::inRandomOrder()->first()?->id ?? FlightAirport::factory()->create()->id,
            'aircraft_type_id' => FlightAircraftType::inRandomOrder()->first()?->id ?? FlightAircraftType::factory()->create()->id,
            'departure_datetime' => $departureTime,
            'arrival_datetime' => (clone $departureTime)->modify("+{$durationMinutes} minutes"),
            'duration_minutes' => $durationMinutes,
            'cabin_class' => $this->faker->randomElement(['economy', 'business']),
            'booking_class' => $this->faker->randomElement(['Y', 'B', 'M', 'K']),
            'fare_basis' => strtoupper($this->faker->bothify('???##')),
            'status' => 'confirmed',
            'baggage_allowance' => '1PC x 23kg',
        ];
    }

    public function outbound(): static
    {
        return $this->state(fn (array $attributes) => [
            'segment_type' => 'outbound',
            'segment_order' => 1,
        ]);
    }

    public function inbound(): static
    {
        return $this->state(fn (array $attributes) => [
            'segment_type' => 'inbound',
            'segment_order' => 2,
        ]);
    }
}
