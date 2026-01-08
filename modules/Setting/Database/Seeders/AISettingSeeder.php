<?php

namespace Modules\Setting\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Setting\Models\AISetting;

class AISettingSeeder extends Seeder
{
    public function run(): void
    {
        AISetting::create([
            'provider' => 'OpenAI',
            'model' => 'gpt-4',
            'api_key' => env('OPENAI_API_KEY'),
            'is_default' => true,
        ]);

        AISetting::create([
            'provider' => 'Google Gemini',
            'model' => 'gemini-pro',
            'api_key' => env('GEMINI_API_KEY'),
            'is_default' => false,
        ]);
    }
}
