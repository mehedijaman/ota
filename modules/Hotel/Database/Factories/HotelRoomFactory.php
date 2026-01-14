<?php

namespace Modules\Hotel\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelRoom;
use Modules\Hotel\Models\HotelRoomType;

class HotelRoomFactory extends Factory
{
    protected $model = HotelRoom::class;

    protected array $roomNameParts = [
        'Superior',
        'Deluxe',
        'Premier',
        'Executive',
        'Signature',
        'Luxury',
        'Classic',
        'Comfort',
    ];

    public function definition(): array
    {
        $namePart = $this->faker->randomElement($this->roomNameParts);
        $view = $this->faker->randomElement(['City View', 'Sea View', 'Garden View', 'Pool View', '']);
        $name = trim("$namePart $view Room");

        return [
            'uuid' => $this->faker->uuid(),
            'hotel_id' => Hotel::factory(),
            'room_type_id' => HotelRoomType::factory(),
            'name' => $name,
            'code' => strtoupper(Str::random(2)).'-'.$this->faker->unique()->numberBetween(100, 999),
            'description' => "Experience comfort in our $name with modern amenities and thoughtful design.",
            'bed_type' => $this->faker->randomElement(['king', 'queen', 'twin', 'double', 'single']),
            'bed_count' => $this->faker->numberBetween(1, 2),
            'bed_configuration' => [
                ['type' => 'king', 'count' => 1],
            ],
            'max_adults' => $maxAdults = $this->faker->randomElement([2, 3, 4]),
            'max_children' => $this->faker->randomElement([1, 2]),
            'max_infants' => 1,
            'max_occupancy' => $maxAdults + 1,
            'standard_occupancy' => 2,
            'room_size_sqm' => $this->faker->randomFloat(1, 20, 80),
            'floor' => $this->faker->randomElement(['low', 'mid', 'high', null]),
            'view_type' => $this->faker->randomElement(['city', 'sea', 'garden', 'pool', 'mountain', null]),
            'is_smoking' => $this->faker->boolean(10),
            'is_accessible' => $this->faker->boolean(20),
            'has_balcony' => $this->faker->boolean(40),
            'has_terrace' => $this->faker->boolean(20),
            'has_kitchen' => $this->faker->boolean(10),
            'has_kitchenette' => $this->faker->boolean(20),
            'total_inventory' => $this->faker->numberBetween(5, 50),
            'images' => [],
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(1, 100),
        ];
    }

    public function suite(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Presidential Suite',
            'max_adults' => 4,
            'max_occupancy' => 6,
            'room_size_sqm' => $this->faker->randomFloat(1, 60, 150),
            'has_balcony' => true,
            'has_kitchen' => true,
        ]);
    }

    public function accessible(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_accessible' => true,
            'floor_level' => 'low',
        ]);
    }
}
