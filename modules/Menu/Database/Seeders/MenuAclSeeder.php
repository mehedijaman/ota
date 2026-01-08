<?php

namespace Modules\Menu\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class MenuAclSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = $this->getPermissions();

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'user',
            ]);
        }
    }

    private function getPermissions(): array
    {
        return [
            'website-menu-list',
            'website-menu-view',
            'website-menu-create',
            'website-menu-edit',
            'website-menu-delete',
        ];
    }
}
