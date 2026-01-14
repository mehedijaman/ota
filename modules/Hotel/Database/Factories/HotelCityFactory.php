<?php

namespace Modules\Hotel\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Hotel\Models\HotelCity;
use Modules\Hotel\Models\HotelCountry;

class HotelCityFactory extends Factory
{
    protected $model = HotelCity::class;

    protected array $bangladeshCities = [
        ['name' => 'Dhaka', 'lat' => 23.8103, 'lng' => 90.4125, 'timezone' => 'Asia/Dhaka', 'iata' => 'DAC'],
        ['name' => 'Chittagong', 'lat' => 22.3569, 'lng' => 91.7832, 'timezone' => 'Asia/Dhaka', 'iata' => 'CGP'],
        ['name' => "Cox's Bazar", 'lat' => 21.4272, 'lng' => 92.0058, 'timezone' => 'Asia/Dhaka', 'iata' => 'CXB'],
        ['name' => 'Sylhet', 'lat' => 24.8949, 'lng' => 91.8687, 'timezone' => 'Asia/Dhaka', 'iata' => 'ZYL'],
        ['name' => 'Khulna', 'lat' => 22.8456, 'lng' => 89.5403, 'timezone' => 'Asia/Dhaka', 'iata' => null],
        ['name' => 'Rajshahi', 'lat' => 24.3636, 'lng' => 88.6241, 'timezone' => 'Asia/Dhaka', 'iata' => 'RJH'],
        ['name' => 'Rangpur', 'lat' => 25.7439, 'lng' => 89.2752, 'timezone' => 'Asia/Dhaka', 'iata' => null],
        ['name' => 'Barisal', 'lat' => 22.7010, 'lng' => 90.3535, 'timezone' => 'Asia/Dhaka', 'iata' => 'BZL'],
        ['name' => 'Comilla', 'lat' => 23.4607, 'lng' => 91.1809, 'timezone' => 'Asia/Dhaka', 'iata' => null],
        ['name' => 'Bandarban', 'lat' => 22.1953, 'lng' => 92.2184, 'timezone' => 'Asia/Dhaka', 'iata' => null],
    ];

    public function definition(): array
    {
        $city = $this->faker->randomElement($this->bangladeshCities);

        return [
            'country_id' => HotelCountry::factory(),
            'name' => $city['name'],
            'slug' => Str::slug($city['name']),
            'latitude' => $city['lat'],
            'longitude' => $city['lng'],
            'timezone' => $city['timezone'],
            'iata_code' => $city['iata'],
            'description' => "Explore the beautiful city of {$city['name']}",
            'is_popular' => $this->faker->boolean(40),
            'is_active' => true,
            'hotels_count' => 0,
            'sort_order' => $this->faker->numberBetween(1, 100),
            'meta_title' => "Hotels in {$city['name']}",
            'meta_description' => "Find the best hotels in {$city['name']}. Book now for the best rates.",
        ];
    }

    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_popular' => true,
        ]);
    }
}
