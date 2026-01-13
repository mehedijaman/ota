<?php

namespace Modules\Visa\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Visa\Models\VisaCategory;

class VisaCategoryFactory extends Factory
{
    protected $model = VisaCategory::class;

    protected static array $categories = [
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

    protected static int $categoryIndex = 0;

    public function definition(): array
    {
        $index = self::$categoryIndex % count(self::$categories);
        $category = self::$categories[$index];
        self::$categoryIndex++;

        $uniqueSuffix = self::$categoryIndex > count(self::$categories)
            ? '-'.self::$categoryIndex
            : '';

        return [
            'name' => $category['name'].$uniqueSuffix,
            'slug' => Str::slug($category['name']).$uniqueSuffix,
            'code' => $category['code'].$uniqueSuffix,
            'description' => fake()->paragraph(),
            'icon' => $category['icon'],
            'color' => $category['color'],
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 100),
            'meta_title' => $category['name'].' Services',
            'meta_description' => 'Professional '.$category['name'].' application services.',
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
