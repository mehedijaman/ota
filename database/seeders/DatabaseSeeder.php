<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Acl\Database\Seeders\AclModelHasRolesSeeder;
use Modules\Acl\Database\Seeders\AclPermissionSeeder;
use Modules\Acl\Database\Seeders\AclRoleSeeder;
use Modules\Agent\Database\Seeders\AgentSeeder;
use Modules\Blog\Database\Seeders\BlogAclSeeder;
use Modules\Blog\Database\Seeders\BlogSeeder;
use Modules\Customer\Database\Seeders\CustomerSeeder;
use Modules\Dashboard\Database\Seeders\DashboardAclSeeder;
use Modules\Employee\Database\Seeders\AreaSeeder;
use Modules\Employee\Database\Seeders\DepartmentSeeder;
use Modules\Employee\Database\Seeders\DesignationSeeder;
use Modules\Employee\Database\Seeders\EmployeeAclSeeder;
use Modules\Employee\Database\Seeders\EmployeeSeeder;
use Modules\Faq\Database\Seeders\FaqAclSeeder;
use Modules\Faq\Database\Seeders\FaqSeeder;
use Modules\Menu\Database\Seeders\MenuAclSeeder;
use Modules\Menu\Database\Seeders\MenuSeeder;
use Modules\Page\Database\Seeders\PageAclSeeder;
use Modules\Page\Database\Seeders\PageSeeder;
use Modules\Setting\Database\Seeders\SettingAclSeeder;
use Modules\Slider\Database\Seeders\SliderAclSeeder;
use Modules\Slider\Database\Seeders\SliderSeeder;
use Modules\Testimonial\Database\Seeders\TestimonialAclSeeder;
use Modules\Testimonial\Database\Seeders\TestimonialSeeder;
use Modules\Tour\Database\Seeders\TourAclSeeder;
use Modules\Tour\Database\Seeders\TourBookingSeeder;
use Modules\Tour\Database\Seeders\TourReviewSeeder;
use Modules\Tour\Database\Seeders\TourSeeder;
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

            // Website
            MenuAclSeeder::class,
            SliderAclSeeder::class,
            TestimonialAclSeeder::class,
            FaqAclSeeder::class,
            PageAclSeeder::class,
            BlogAclSeeder::class,
            SettingAclSeeder::class,

            // Modules
            DashboardAclSeeder::class,
            EmployeeAclSeeder::class,
            TourAclSeeder::class,
        ]);

        // Data Seeders
        $this->call([
            MenuSeeder::class,
            SliderSeeder::class,
            TestimonialSeeder::class,
            FaqSeeder::class,
            PageSeeder::class,
            BlogSeeder::class,

            DesignationSeeder::class,
            DepartmentSeeder::class,
            AreaSeeder::class,
            EmployeeSeeder::class,
            CustomerSeeder::class,
            AgentSeeder::class,
            TourSeeder::class,
            TourBookingSeeder::class,
            TourReviewSeeder::class,
        ]);
    }
}
