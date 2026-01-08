<?php

namespace Modules\Message\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class MessageAclSeeder extends Seeder
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
            'message-list',
            'message-view',
            'message-create',
            'message-edit',
            'message-delete',
            'message-recycle-bin-list',
            'message-recycle-bin-delete',
            'message-recycle-bin-restore',
        ];
    }
}
