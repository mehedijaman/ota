<?php

namespace Modules\Employee\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Employee\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Human Resources', 'code' => 'HR'],
            ['name' => 'Finance', 'code' => 'FIN'],
            ['name' => 'Marketing', 'code' => 'MKT'],
            ['name' => 'Sales', 'code' => 'SLS'],
            ['name' => 'Engineering', 'code' => 'ENG'],
            ['name' => 'Operations', 'code' => 'OPS'],
            ['name' => 'Information Technology', 'code' => 'IT'],
            ['name' => 'Customer Support', 'code' => 'SUP'],
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(
                ['code' => $department['code']],
                ['name' => $department['name'], 'is_active' => true]
            );
        }
    }
}
