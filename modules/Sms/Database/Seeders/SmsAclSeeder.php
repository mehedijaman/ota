<?php

namespace Modules\Sms\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class SmsAclSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = $this->getPermissions();

        foreach ($permissions as $permission => $scope) {
            Permission::updateOrCreate(
                [
                    'name' => $permission,
                    'guard_name' => 'user',
                ],
                [
                    'permission_scope' => $scope,
                ]
            );
        }
    }

    private function getPermissions(): array
    {
        return [
            // Tenant SMS
            'menu-sms' => 'tenant',
            'sms-wallet-view' => 'tenant',
            'sms-send-promotional' => 'tenant',
            'sms-due-reminder-send' => 'tenant',
            'sms-settings-tenant' => 'tenant',

            // Superadmin SMS
            'menu-sms-superadmin' => 'superadmin',
            'sms-package-create' => 'superadmin',
            'sms-package-view' => 'superadmin',
            'sms-package-edit' => 'superadmin',
            'sms-package-delete' => 'superadmin',
            'sms-purchase-view' => 'superadmin',
            'sms-purchase-approve' => 'superadmin',
            'sms-purchase-reject' => 'superadmin',
            'sms-settings' => 'superadmin',
        ];
    }
}
