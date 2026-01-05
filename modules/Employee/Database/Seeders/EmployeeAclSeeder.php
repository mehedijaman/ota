<?php

namespace Modules\Employee\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class EmployeeAclSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = $this->getPermissions();

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                [
                    'name' => $permission,
                ],
                [
                    'guard_name' => 'user',
                ]
            );
        }
    }

    private function getPermissions(): array
    {
        return [
            'employee',

            'employee-list',
            'employee-create',
            'employee-view',
            'employee-edit',
            'employee-delete',

            'employee-designation-list',
            'employee-designation-create',
            'employee-designation-view',
            'employee-designation-edit',
            'employee-designation-delete',

            'employee-department-list',
            'employee-department-create',
            'employee-department-view',
            'employee-department-edit',
            'employee-department-delete',

            'employee-area-list',
            'employee-area-create',
            'employee-area-view',
            'employee-area-edit',
            'employee-area-delete',
        ];
    }
}
