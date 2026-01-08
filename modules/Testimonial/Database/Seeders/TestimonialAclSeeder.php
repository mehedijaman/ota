<?php

namespace Modules\Testimonial\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class TestimonialAclSeeder extends Seeder
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
            'testimonial-list',
            'testimonial-view',
            'testimonial-create',
            'testimonial-edit',
            'testimonial-delete',
            'testimonial-recycle-bin-list',
            'testimonial-recycle-bin-delete',
            'testimonial-recycle-bin-restore',
        ];
    }
}
