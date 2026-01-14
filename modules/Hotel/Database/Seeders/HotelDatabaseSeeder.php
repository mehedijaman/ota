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
            HotelSupplierSeeder::class,
            HotelCountrySeeder::class,
            HotelCitySeeder::class,
            HotelRoomTypeSeeder::class,
            HotelAmenitySeeder::class,
            HotelRoomAmenitySeeder::class,
            HotelAclSeeder::class,
        ]);
    }
}
