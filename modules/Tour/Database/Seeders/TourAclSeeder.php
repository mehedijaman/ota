<?php

namespace Modules\Tour\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class TourAclSeeder extends Seeder
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
            'tour',

            'tour-list',
            'tour-view',
            'tour-create',
            'tour-edit',
            'tour-delete',

            'tour-category-list',
            'tour-category-view',
            'tour-category-create',
            'tour-category-edit',
            'tour-category-delete',

            'tour-destination-list',
            'tour-destination-view',
            'tour-destination-create',
            'tour-destination-edit',
            'tour-destination-delete',

            'tour-review-list',
            'tour-review-view',
            'tour-review-create',
            'tour-review-edit',
            'tour-review-delete',

            'tour-booking-list',
            'tour-booking-view',
            'tour-booking-create',
            'tour-booking-edit',
            'tour-booking-delete',
        ];
    }
}
