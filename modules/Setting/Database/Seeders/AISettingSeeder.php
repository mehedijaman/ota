<?php

namespace Modules\Setting\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Setting\Models\AISetting;

use function Laravel\Prompts\info;

class AISettingSeeder extends Seeder
{
    public function run(): void
    {
        info('Creating AI Settings...');

        AISetting::firstOrCreate(
            ['provider' => 'OpenAI', 'model' => 'gpt-4o'],
            [
                'api_key' => config('services.openai.api_key', ''),
                'is_default' => true,
            ]
        );

        AISetting::firstOrCreate(
            ['provider' => 'Google Gemini', 'model' => 'gemini-pro'],
            [
                'api_key' => config('services.google.gemini_api_key', ''),
                'is_default' => false,
            ]
        );

        info('AI Settings created.');
    }
}
