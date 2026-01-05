<?php

namespace Modules\Employee\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Employee\Models\Area;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            ['name' => 'Head Office', 'code' => 'HO'],
            ['name' => 'Branch Office', 'code' => 'BO'],
            ['name' => 'Factory', 'code' => 'FAC'],
            ['name' => 'Warehouse', 'code' => 'WH'],
            ['name' => 'Remote', 'code' => 'RMT'],
            ['name' => 'Field Operations', 'code' => 'FLD'],
        ];

        foreach ($areas as $area) {
            Area::firstOrCreate(
                ['code' => $area['code']],
                ['name' => $area['name'], 'is_active' => true]
            );
        }
    }
}
