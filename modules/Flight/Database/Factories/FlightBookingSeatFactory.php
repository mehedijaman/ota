<?php

namespace Modules\Flight\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Flight\Models\FlightBooking;
use Modules\Flight\Models\FlightBookingPassenger;
use Modules\Flight\Models\FlightBookingSeat;
use Modules\Flight\Models\FlightBookingSegment;

class FlightBookingSeatFactory extends Factory
{
    protected $model = FlightBookingSeat::class;

    public function definition(): array
    {
        $row = $this->faker->numberBetween(1, 40);
        $seat = $this->faker->randomElement(['A', 'B', 'C', 'D', 'E', 'F']);

        return [
            'booking_id' => FlightBooking::factory(),
            'segment_id' => FlightBookingSegment::factory(),
            'passenger_id' => FlightBookingPassenger::factory(),
            'seat_number' => $row.$seat,
            'seat_type' => in_array($seat, ['A', 'F']) ? 'window' : (in_array($seat, ['C', 'D']) ? 'aisle' : 'middle'),
            'row_number' => $row,
            'is_window' => in_array($seat, ['A', 'F']),
            'is_aisle' => in_array($seat, ['C', 'D']),
            'is_exit_row' => in_array($row, [10, 11, 25, 26]),
            'has_extra_legroom' => in_array($row, [1, 10, 11, 25, 26]),
            'price' => $this->faker->randomElement([0, 500, 1000, 1500, 2000]),
            'currency_code' => 'BDT',
            'is_complimentary' => $this->faker->boolean(30),
            'status' => 'confirmed',
        ];
    }
}
