<?php

namespace Modules\Faq\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class FaqAclSeeder extends Seeder
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
            'website-faq-list',
            'website-faq-view',
            'website-faq-create',
            'website-faq-edit',
            'website-faq-delete',
        ];
    }
}
