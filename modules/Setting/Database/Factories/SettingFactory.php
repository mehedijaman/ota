<?php

namespace Modules\Setting\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Setting\Models\Setting;

class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition(): array
    {
        return [
            // Site Identity
            'site_name' => fake()->company(),
            'slogan' => fake()->catchPhrase(),
            'logo' => null,
            'favicon' => null,
            'banner' => null,

            // Display Options
            'show_name' => true,
            'show_slogan' => true,
            'show_logo' => true,

            // Contact Information
            'addresses' => [
                ['label' => 'Head Office', 'address' => fake()->address()],
            ],
            'phones' => [
                ['label' => 'Main', 'number' => fake()->phoneNumber()],
            ],
            'emails' => [
                ['label' => 'General', 'email' => fake()->companyEmail()],
            ],
            'whatsapp' => fake()->phoneNumber(),
            'google_map' => null,

            // Social Media
            'facebook' => 'https://facebook.com/'.fake()->userName(),
            'twitter' => 'https://twitter.com/'.fake()->userName(),
            'instagram' => 'https://instagram.com/'.fake()->userName(),
            'youtube' => null,
            'linkedin' => null,
            'pinterest' => null,

            // Theme
            'navbar_background' => '#ffffff',
            'navbar_text_color' => '#1e293b',
            'body_background' => '#f8fafc',
            'body_text_color' => '#334155',
            'footer_background' => '#1e293b',
            'footer_text_color' => '#f1f5f9',

            // Advanced
            'custom_css' => null,
        ];
    }

    public function minimal(): static
    {
        return $this->state(fn (array $attributes) => [
            'site_name' => 'My Site',
            'addresses' => null,
            'phones' => null,
            'emails' => null,
            'facebook' => null,
            'twitter' => null,
            'instagram' => null,
        ]);
    }
}
