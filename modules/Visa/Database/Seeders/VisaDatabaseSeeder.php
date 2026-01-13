<?php

namespace Modules\Visa\Database\Seeders;

use Illuminate\Database\Seeder;

class VisaDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            VisaCountrySeeder::class,
            VisaCategorySeeder::class,
            VisaSeeder::class,
            VisaApplicationSeeder::class,
        ]);
    }
}
