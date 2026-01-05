<?php

namespace Modules\Employee\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Employee\Models\Designation;

class DesignationSeeder extends Seeder
{
    public function run(): void
    {
        $designations = [
            ['name' => 'Chief Executive Officer', 'code' => 'CEO'],
            ['name' => 'Chief Operating Officer', 'code' => 'COO'],
            ['name' => 'Chief Financial Officer', 'code' => 'CFO'],
            ['name' => 'Chief Technology Officer', 'code' => 'CTO'],
            ['name' => 'General Manager', 'code' => 'GM'],
            ['name' => 'Manager', 'code' => 'MGR'],
            ['name' => 'Assistant Manager', 'code' => 'AM'],
            ['name' => 'Senior Executive', 'code' => 'SE'],
            ['name' => 'Executive', 'code' => 'EXE'],
            ['name' => 'Junior Executive', 'code' => 'JE'],
            ['name' => 'Supervisor', 'code' => 'SUP'],
            ['name' => 'Team Lead', 'code' => 'TL'],
            ['name' => 'Software Engineer', 'code' => 'SWE'],
            ['name' => 'Accountant', 'code' => 'ACC'],
            ['name' => 'HR Officer', 'code' => 'HRO'],
        ];

        foreach ($designations as $designation) {
            Designation::firstOrCreate(
                ['code' => $designation['code']],
                ['name' => $designation['name'], 'is_active' => true]
            );
        }
    }
}
