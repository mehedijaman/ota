<?php

namespace Modules\Tour\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Tour\Models\TourCategory;

class TourCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Beach & Island', 'icon' => 'sun', 'sort_order' => 1],
            ['name' => 'Adventure', 'icon' => 'compass', 'sort_order' => 2],
            ['name' => 'Cultural & Heritage', 'icon' => 'landmark', 'sort_order' => 3],
            ['name' => 'Wildlife & Safari', 'icon' => 'paw-print', 'sort_order' => 4],
            ['name' => 'Mountain & Trekking', 'icon' => 'mountain', 'sort_order' => 5],
            ['name' => 'City Tours', 'icon' => 'building', 'sort_order' => 6],
            ['name' => 'Honeymoon', 'icon' => 'heart', 'sort_order' => 7],
            ['name' => 'Family Package', 'icon' => 'users', 'sort_order' => 8],
        ];

        foreach ($categories as $category) {
            TourCategory::firstOrCreate(
                ['name' => $category['name']],
                [
                    'slug' => \Illuminate\Support\Str::slug($category['name']),
                    'icon' => $category['icon'],
                    'sort_order' => $category['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
