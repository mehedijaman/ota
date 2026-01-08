<?php

namespace Modules\Message\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Modules\Message\Models\Message;

use function Laravel\Prompts\info;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        info('Creating Messages...');
        Message::factory()->count(10)->create();
        info('Messages created.');

        Schema::enableForeignKeyConstraints();
    }
}
