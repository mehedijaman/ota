<?php

namespace Modules\Hotel\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelChain;
use Modules\Hotel\Models\HotelCity;
use Modules\Hotel\Models\HotelCountry;
use Modules\Hotel\Models\HotelSupplier;

class HotelFactory extends Factory
{
    protected $model = Hotel::class;

    protected array $hotelNames = [
        'The Grand',
        'Royal Palace',
        'Seaside Resort',
        'Mountain View',
        'City Center',
        'Business Park',
        'Garden Inn',
        'Ocean Breeze',
        'Sunset Beach',
        'Heritage House',
        'Luxury Suites',
        'Executive Tower',
        'Paradise Resort',
        'Golden Sands',
        'Blue Horizon',
        'Palm Grove',
        'Sunrise Point',
        'Crystal Waters',
        'Emerald Bay',
        'Silver Springs',
    ];

    protected array $suffixes = [
        'Hotel',
        'Resort',
        'Inn',
        'Suites',
        'Lodge',
        'Residence',
        'Plaza',
        'Palace',
    ];

    public function definition(): array
    {
        $namePart = $this->faker->randomElement($this->hotelNames);
        $suffix = $this->faker->randomElement($this->suffixes);
        $name = "$namePart $suffix";
        $starRating = $this->faker->randomElement([3, 4, 5]);

        return [
            'uuid' => $this->faker->uuid(),
            'supplier_id' => null,
            'supplier_hotel_id' => null,
            'chain_id' => $this->faker->optional(0.3)->randomElement(HotelChain::pluck('id')->toArray() ?: [null]),
            'country_id' => HotelCountry::factory(),
            'city_id' => HotelCity::factory(),
            'area_id' => null,
            'name' => $name,
            'slug' => Str::slug($name).'-'.$this->faker->unique()->numberBetween(1, 9999),
            'code' => strtoupper(Str::random(3)).'-'.str_pad($this->faker->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
            'star_rating' => $starRating,
            'property_type' => $this->faker->randomElement(['hotel', 'resort', 'apartment', 'villa', 'boutique']),
            'excerpt' => $this->faker->sentence(15),
            'description' => $this->generateDescription($name, $starRating),
            'address' => $this->faker->streetAddress(),
            'postal_code' => $this->faker->postcode(),
            'latitude' => $this->faker->latitude(20, 26),
            'longitude' => $this->faker->longitude(88, 93),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->companyEmail(),
            'website' => $this->faker->url(),
            'check_in_time' => '14:00',
            'check_out_time' => '12:00',
            'total_rooms' => $this->faker->numberBetween(20, 200),
            'total_floors' => $this->faker->numberBetween(3, 25),
            'year_built' => $this->faker->year('-30 years'),
            'year_renovated' => $this->faker->optional(0.5)->year('-5 years'),
            'policies' => $this->generatePolicies(),
            'is_featured' => $this->faker->boolean(20),
            'is_recommended' => $this->faker->boolean(15),
            'is_active' => true,
            'is_verified' => $this->faker->boolean(80),
            'is_instant_bookable' => $this->faker->boolean(70),
            'avg_rating' => $this->faker->randomFloat(2, 3.5, 5),
            'reviews_count' => $this->faker->numberBetween(0, 500),
            'bookings_count' => $this->faker->numberBetween(0, 2000),
            'commission_rate' => $this->faker->randomFloat(2, 10, 20),
            'markup_rate' => $this->faker->randomFloat(2, 5, 15),
            'meta_title' => "$name | Book Now",
            'meta_description' => "Book $name for the best rates. $starRating-star hotel with excellent amenities.",
        ];
    }

    protected function generateDescription(string $name, int $stars): string
    {
        $intro = "Welcome to $name, a prestigious $stars-star property offering exceptional hospitality.";
        $para1 = $this->faker->paragraph(3);
        $para2 = $this->faker->paragraph(2);

        return "<p>$intro</p><p>$para1</p><p>$para2</p>";
    }

    protected function generatePolicies(): array
    {
        return [
            'smoking' => 'No smoking in rooms. Designated smoking areas available.',
            'pets' => $this->faker->randomElement(['Pets not allowed', 'Pets allowed on request']),
            'children' => 'Children of all ages are welcome.',
            'cancellation' => 'Free cancellation up to 48 hours before check-in.',
            'payment' => 'Credit card required at check-in.',
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    public function fiveStars(): static
    {
        return $this->state(fn (array $attributes) => [
            'star_rating' => 5,
        ]);
    }

    public function withSupplier(): static
    {
        return $this->state(fn (array $attributes) => [
            'supplier_id' => HotelSupplier::factory(),
            'supplier_hotel_id' => 'SUP-'.$this->faker->unique()->numberBetween(10000, 99999),
        ]);
    }
}
