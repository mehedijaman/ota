<?php

namespace Modules\Tour\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Tour\Models\TourDestination;

class TourDestinationFactory extends Factory
{
    protected $model = TourDestination::class;

    public function definition(): array
    {
        $destinations = [
            ['name' => "Cox's Bazar", 'country' => 'Bangladesh', 'city' => "Cox's Bazar", 'lat' => 21.4272, 'lng' => 92.0058],
            ['name' => 'Sundarbans', 'country' => 'Bangladesh', 'city' => 'Khulna', 'lat' => 21.9497, 'lng' => 89.1833],
            ['name' => 'Sajek Valley', 'country' => 'Bangladesh', 'city' => 'Rangamati', 'lat' => 23.3817, 'lng' => 92.2936],
            ['name' => 'Bandarban', 'country' => 'Bangladesh', 'city' => 'Bandarban', 'lat' => 22.1953, 'lng' => 92.2184],
            ['name' => 'Saint Martin', 'country' => 'Bangladesh', 'city' => 'Teknaf', 'lat' => 20.6273, 'lng' => 92.3226],
            ['name' => 'Sylhet', 'country' => 'Bangladesh', 'city' => 'Sylhet', 'lat' => 24.8949, 'lng' => 91.8687],
            ['name' => 'Rangamati', 'country' => 'Bangladesh', 'city' => 'Rangamati', 'lat' => 22.6372, 'lng' => 92.2061],
            ['name' => 'Kuakata', 'country' => 'Bangladesh', 'city' => 'Patuakhali', 'lat' => 21.8167, 'lng' => 90.1167],
        ];

        $dest = $this->faker->randomElement($destinations);

        return [
            'name' => $dest['name'],
            'slug' => Str::slug($dest['name']),
            'country' => $dest['country'],
            'city' => $dest['city'],
            'description' => $this->faker->paragraph(3),
            'latitude' => $dest['lat'],
            'longitude' => $dest['lng'],
            'is_featured' => $this->faker->boolean(30),
            'is_active' => true,
            'meta_title' => $dest['name'].' Tours',
            'meta_description' => $this->faker->sentence(),
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
}
