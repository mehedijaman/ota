<?php

namespace Modules\Tour\Database\Seeders;

use Illuminate\Database\Seeder;

class TourDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TourSeeder::class,
            TourBookingSeeder::class,
            TourReviewSeeder::class,
        ]);
    }
}
