<?php

namespace Modules\Setting\Http\Requests;

use Modules\Support\Http\Requests\Request;

class SettingValidate extends Request
{
    public function rules(): array
    {
        return [
            // Site Identity
            'site_name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'slogan' => 'nullable|string|max:255',

            // Display Options
            'show_name' => 'nullable|boolean',
            'show_slogan' => 'nullable|boolean',
            'show_logo' => 'nullable|boolean',

            // Media uploads
            'logo' => 'nullable|image|max:2048',
            'favicon' => 'nullable|image|max:2048',
            'banner' => 'nullable|image|max:2048',
            'watermark' => 'nullable|image|max:2048',
            'remove_previous_logo' => 'nullable|boolean',
            'remove_previous_favicon' => 'nullable|boolean',
            'remove_previous_banner' => 'nullable|boolean',
            'remove_previous_watermark' => 'nullable|boolean',

            // Contact Information
            'addresses' => 'nullable|array',
            'addresses.*.label' => 'nullable|string|max:255',
            'addresses.*.address' => 'nullable|string|max:500',
            'phones' => 'nullable|array',
            'phones.*.label' => 'nullable|string|max:255',
            'phones.*.number' => 'nullable|string|max:50',
            'emails' => 'nullable|array',
            'emails.*.label' => 'nullable|string|max:255',
            'emails.*.email' => 'nullable|email|max:255',
            'whatsapp' => 'nullable|string|max:50',
            'google_map' => 'nullable|string',

            // Social Media Links
            'facebook' => 'nullable|string|max:255|url',
            'twitter' => 'nullable|string|max:255|url',
            'instagram' => 'nullable|string|max:255|url',
            'youtube' => 'nullable|string|max:255|url',
            'linkedin' => 'nullable|string|max:255|url',
            'pinterest' => 'nullable|string|max:255|url',

            // Theme Customization
            'navbar_background' => 'nullable|string|max:20',
            'navbar_text_color' => 'nullable|string|max:20',
            'body_background' => 'nullable|string|max:20',
            'body_text_color' => 'nullable|string|max:20',
            'footer_background' => 'nullable|string|max:20',
            'footer_text_color' => 'nullable|string|max:20',

            // SMTP Settings
            'smtp_settings' => 'nullable|array',
            'smtp_settings.host' => 'nullable|string|max:255',
            'smtp_settings.port' => 'nullable|numeric|min:1|max:65535',
            'smtp_settings.username' => 'nullable|string|max:255',
            'smtp_settings.password' => 'nullable|string|max:255',
            'smtp_settings.encryption' => 'nullable|string|in:,tls,ssl',
            'smtp_settings.from_address' => 'nullable|email|max:255',
            'smtp_settings.from_name' => 'nullable|string|max:255',

            // Advanced
            'custom_css' => 'nullable|string',
        ];
    }
}
