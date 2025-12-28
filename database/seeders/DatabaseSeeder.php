<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Blog\Database\Seeders\BlogSeeder;
use Modules\Acl\Database\Seeders\AclRoleSeeder;
use Modules\Blog\Database\Seeders\BlogAclSeeder;
use Modules\Acl\Database\Seeders\AclPermissionSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AclRoleSeeder::class,
            AclPermissionSeeder::class,
        ]);

        $this->call(BlogAclSeeder::class);
        $this->call(BlogSeeder::class);
    }
}
