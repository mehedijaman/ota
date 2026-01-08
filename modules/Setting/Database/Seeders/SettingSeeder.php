<?php

namespace Modules\Setting\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Setting\Services\SettingService;

use function Laravel\Prompts\info;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        info('Creating Site Settings...');

        SettingService::seedDefaults();

        info('Site Settings created.');
    }
}
