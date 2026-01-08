<?php

namespace Modules\Setting\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class SettingAclSeeder extends Seeder
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
            'website-setting-list',
            'website-setting-view',
            'website-setting-create',
            'website-setting-edit',
            'website-setting-delete',
        ];
    }
}
