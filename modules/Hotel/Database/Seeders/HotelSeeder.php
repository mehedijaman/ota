<?php

namespace Modules\Hotel\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelAmenity;
use Modules\Hotel\Models\HotelCity;
use Modules\Hotel\Models\HotelCountry;
use Modules\Hotel\Models\HotelRoom;
use Modules\Hotel\Models\HotelRoomType;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have countries and cities first
        $countries = HotelCountry::all();
        $cities = HotelCity::all();
        $roomTypes = HotelRoomType::all();
        $amenities = HotelAmenity::all();

        if ($countries->isEmpty() || $cities->isEmpty()) {
            $this->command->warn('Please run HotelCountrySeeder and HotelCitySeeder first.');

            return;
        }

        // Create 50 hotels
        Hotel::factory()
            ->count(50)
            ->create([
                'country_id' => fn () => $countries->random()->id,
                'city_id' => fn () => $cities->random()->id,
            ])
            ->each(function (Hotel $hotel) use ($roomTypes, $amenities) {
                // Attach random amenities to each hotel
                if ($amenities->isNotEmpty()) {
                    $hotel->amenities()->attach(
                        $amenities->random(rand(5, 15))->pluck('id')
                    );
                }

                // Create 2-5 rooms per hotel
                if ($roomTypes->isNotEmpty()) {
                    $roomCount = rand(2, 5);
                    for ($i = 0; $i < $roomCount; $i++) {
                        HotelRoom::factory()->create([
                            'hotel_id' => $hotel->id,
                            'room_type_id' => $roomTypes->random()->id,
                        ]);
                    }
                }
            });

        $this->command->info('Created 50 hotels with rooms and amenities.');
    }
}
