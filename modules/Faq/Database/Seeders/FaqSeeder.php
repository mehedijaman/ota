<?php

namespace Modules\Faq\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Faq\Models\Faq;
use Modules\Faq\Models\FaqCategory;

use function Laravel\Prompts\info;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        info('Creating FAQ Categories...');

        $categories = FaqCategory::factory()->count(4)->active()->create();

        info('Creating FAQs...');

        foreach ($categories as $category) {
            Faq::factory()
                ->count(fake()->numberBetween(3, 6))
                ->forCategory($category)
                ->published()
                ->create();
        }

        Faq::factory()->count(2)->featured()->published()->create();

        info('FAQs created.');
    }
}
