<?php

namespace Modules\Page\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Page\Models\Page;

use function Laravel\Prompts\info;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        info('Creating Pages...');

        Page::factory()->count(5)->create();
        Page::factory()->count(2)->draft()->create();

        info('Pages created.');
    }
}
