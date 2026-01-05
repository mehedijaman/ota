<?php

namespace Modules\Acl\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

class AclPermissionSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        Permission::truncate();

        $permissions = $this->getPermissions();

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'user',
            ]);
        }

        Schema::enableForeignKeyConstraints();
    }

    private function getPermissions(): array
    {
        return [
            'acl',
            'acl-user-list',
            'acl-permission-list',
            'acl-role-list',
            'acl-user-role-edit',
            'acl-user-permission-edit',
            'acl-user-create',
            'acl-user-edit',
            'acl-user-delete',
            'acl-permission-create',
            'acl-permission-edit',
            'acl-permission-delete',
            'acl-role-create',
            'acl-role-edit',
            'acl-role-delete',
            'acl-role-permission-edit',
        ];

    }
}
