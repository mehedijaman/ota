<?php

namespace Modules\Backup\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class BackupAclSeeder extends Seeder
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
            'backup-list',
            'backup-create',
            'backup-download',
            'backup-delete',
        ];
    }
}
