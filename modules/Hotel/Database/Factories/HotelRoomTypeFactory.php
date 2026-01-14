<?php

namespace Modules\Hotel\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Hotel\Models\HotelRoomType;

class HotelRoomTypeFactory extends Factory
{
    protected $model = HotelRoomType::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word().' Room',
            'code' => strtoupper($this->faker->unique()->lexify('??')),
            'description' => $this->faker->sentence(),
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(1, 10),
        ];
    }
}
