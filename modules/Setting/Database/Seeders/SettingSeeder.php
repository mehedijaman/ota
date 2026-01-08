<?php

namespace Modules\Setting\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Setting\Models\Setting;

use function Laravel\Prompts\info;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        info('Creating Site Settings...');

        Setting::firstOrCreate(
            ['id' => 1],
            [
                // Site Identity
                'site_name' => 'TriptonicBD',
                'slogan' => 'Your Travel Partner',
                'logo' => null,
                'favicon' => null,
                'banner' => null,

                // Display Options
                'show_name' => true,
                'show_slogan' => true,
                'show_logo' => true,

                // Contact Information
                'addresses' => [
                    ['label' => 'Head Office', 'address' => 'Dhaka, Bangladesh'],
                ],
                'phones' => [
                    ['label' => 'Hotline', 'number' => '+880 1700-000000'],
                ],
                'emails' => [
                    ['label' => 'General', 'email' => 'info@triptonicbd.com'],
                    ['label' => 'Support', 'email' => 'support@triptonicbd.com'],
                ],
                'whatsapp' => '+880 1700-000000',
                'google_map' => null,

                // Social Media
                'facebook' => 'https://facebook.com/triptonicbd',
                'twitter' => null,
                'instagram' => 'https://instagram.com/triptonicbd',
                'youtube' => null,
                'linkedin' => null,
                'pinterest' => null,

                // Theme Colors
                'navbar_background' => '#ffffff',
                'navbar_text_color' => '#1e293b',
                'body_background' => '#f8fafc',
                'body_text_color' => '#334155',
                'footer_background' => '#1e293b',
                'footer_text_color' => '#f1f5f9',

                // Advanced
                'custom_css' => null,
            ]
        );

        info('Site Settings created.');
    }
}
