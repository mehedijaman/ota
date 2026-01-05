<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Acl\Database\Seeders\AclModelHasRolesSeeder;
use Modules\Acl\Database\Seeders\AclPermissionSeeder;
use Modules\Acl\Database\Seeders\AclRoleSeeder;
use Modules\Blog\Database\Seeders\BlogAclSeeder;
use Modules\Blog\Database\Seeders\BlogSeeder;
use Modules\Dashboard\Database\Seeders\DashboardAclSeeder;
use Modules\Employee\Database\Seeders\AreaSeeder;
use Modules\Employee\Database\Seeders\DepartmentSeeder;
use Modules\Employee\Database\Seeders\DesignationSeeder;
use Modules\Employee\Database\Seeders\EmployeeAclSeeder;
use Modules\Employee\Database\Seeders\EmployeeSeeder;
use Modules\User\Database\Seeders\UserSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ACL Seeders
        $this->call([
            AclRoleSeeder::class,
            AclPermissionSeeder::class,
            UserSeeder::class,
            AclModelHasRolesSeeder::class,
            DashboardAclSeeder::class,
            BlogAclSeeder::class,
            EmployeeAclSeeder::class,
        ]);

        // Data Seeders
        $this->call([
            BlogSeeder::class,
            DesignationSeeder::class,
            DepartmentSeeder::class,
            AreaSeeder::class,
            EmployeeSeeder::class,
        ]);
    }
}
