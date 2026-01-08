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
            'facebook' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'pinterest' => 'nullable|string|max:255',

            // Theme Customization
            'navbar_background' => 'nullable|string|max:20',
            'navbar_text_color' => 'nullable|string|max:20',
            'body_background' => 'nullable|string|max:20',
            'body_text_color' => 'nullable|string|max:20',
            'footer_background' => 'nullable|string|max:20',
            'footer_text_color' => 'nullable|string|max:20',

            // SMTP Settings (flat structure)
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|numeric|min:1|max:65535',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'smtp_encryption' => 'nullable|string|in:,tls,ssl',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name' => 'nullable|string|max:255',

            // Advanced
            'custom_css' => 'nullable|string',

            // reCAPTCHA
            'recaptcha_enabled' => 'nullable|boolean',
            'recaptcha_site_key' => 'nullable|string|max:255',
            'recaptcha_secret_key' => 'nullable|string|max:255',
            'recaptcha_version' => 'nullable|string|in:v2,v3',
        ];
    }
}
