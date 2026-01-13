<?php

namespace Modules\Visa\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class VisaAclSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = $this->getPermissions();

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'user',
            ]);
        }
    }

    private function getPermissions(): array
    {
        return [
            'visa',

            'visa-list',
            'visa-view',
            'visa-create',
            'visa-edit',
            'visa-delete',

            'visa-country-list',
            'visa-country-view',
            'visa-country-create',
            'visa-country-edit',
            'visa-country-delete',

            'visa-category-list',
            'visa-category-view',
            'visa-category-create',
            'visa-category-edit',
            'visa-category-delete',

            'visa-application-list',
            'visa-application-view',
            'visa-application-create',
            'visa-application-edit',
            'visa-application-delete',

            'visa-review-list',
            'visa-review-view',
            'visa-review-create',
            'visa-review-edit',
            'visa-review-delete',
        ];
    }
}
