<?php

namespace Modules\Hotel\Database\Seeders;

use Illuminate\Database\Seeder;

class HotelDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            // Base data seeders
            HotelSupplierSeeder::class,
            HotelCitySeeder::class,
            HotelRoomTypeSeeder::class,
            HotelAmenitySeeder::class,
            HotelRoomAmenitySeeder::class,
            HotelAclSeeder::class,

            // Data seeders (50 hotels, 50 reviews, 10 bookings)
            HotelSeeder::class,
            HotelReviewSeeder::class,
            HotelBookingSeeder::class,
        ]);
    }
}
