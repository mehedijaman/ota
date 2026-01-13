<?php

namespace Modules\Visa\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Visa\Models\VisaCategory;

class VisaCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Tourist Visa', 'code' => 'TOURIST', 'icon' => 'camera', 'color' => '#3B82F6'],
            ['name' => 'Business Visa', 'code' => 'BUSINESS', 'icon' => 'briefcase', 'color' => '#10B981'],
            ['name' => 'Work Visa', 'code' => 'WORK', 'icon' => 'building', 'color' => '#F59E0B'],
            ['name' => 'Student Visa', 'code' => 'STUDENT', 'icon' => 'graduation-cap', 'color' => '#8B5CF6'],
            ['name' => 'Medical Visa', 'code' => 'MEDICAL', 'icon' => 'heart-pulse', 'color' => '#EF4444'],
            ['name' => 'Transit Visa', 'code' => 'TRANSIT', 'icon' => 'plane', 'color' => '#6366F1'],
            ['name' => 'Family Visa', 'code' => 'FAMILY', 'icon' => 'users', 'color' => '#EC4899'],
            ['name' => 'Pilgrimage Visa', 'code' => 'PILGRIMAGE', 'icon' => 'moon', 'color' => '#14B8A6'],
            ['name' => 'Conference Visa', 'code' => 'CONFERENCE', 'icon' => 'presentation', 'color' => '#F97316'],
            ['name' => 'Journalist Visa', 'code' => 'JOURNALIST', 'icon' => 'newspaper', 'color' => '#64748B'],
        ];

        foreach ($categories as $index => $category) {
            VisaCategory::firstOrCreate(
                ['code' => $category['code']],
                [
                    'name' => $category['name'],
                    'slug' => Str::slug($category['name']),
                    'description' => 'Professional '.$category['name'].' application services with expert guidance and high success rate.',
                    'icon' => $category['icon'],
                    'color' => $category['color'],
                    'is_active' => true,
                    'sort_order' => $index + 1,
                    'meta_title' => $category['name'].' Services',
                    'meta_description' => 'Apply for '.$category['name'].' with our professional services.',
                ]
            );
        }
    }
}
