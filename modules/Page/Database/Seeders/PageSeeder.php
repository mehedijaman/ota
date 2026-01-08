<?php

namespace Modules\Page\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Modules\Page\Models\Page;

use function Laravel\Prompts\info;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        info('Creating Pages...');
        Page::factory()->count(10)->create();
        info('Pages created.');

        Schema::enableForeignKeyConstraints();
    }
}
