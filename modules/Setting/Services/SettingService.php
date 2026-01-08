<?php

namespace Modules\Setting\Services;

use Illuminate\Http\Request;
use Modules\Setting\Models\Setting;

class SettingService
{
    /**
     * Define all setting groups with their keys, types, and metadata.
     *
     * @return array<string, array<string, array{type: string, default: mixed, is_public?: bool, is_encrypted?: bool, label?: string}>>
     */
    public static function getSettingDefinitions(): array
    {
        return [
            'site' => [
                'site_name' => ['type' => 'string', 'default' => 'TriptonicBD', 'is_public' => true, 'label' => 'Site Name'],
                'description' => ['type' => 'string', 'default' => '', 'is_public' => true, 'label' => 'Site Description'],
                'slogan' => ['type' => 'string', 'default' => '', 'is_public' => true, 'label' => 'Slogan'],
                'show_name' => ['type' => 'boolean', 'default' => true, 'is_public' => true, 'label' => 'Show Site Name'],
                'show_slogan' => ['type' => 'boolean', 'default' => true, 'is_public' => true, 'label' => 'Show Slogan'],
                'show_logo' => ['type' => 'boolean', 'default' => true, 'is_public' => true, 'label' => 'Show Logo'],
            ],
            'contact' => [
                'addresses' => ['type' => 'json', 'default' => [], 'is_public' => true, 'label' => 'Addresses'],
                'phones' => ['type' => 'json', 'default' => [], 'is_public' => true, 'label' => 'Phone Numbers'],
                'emails' => ['type' => 'json', 'default' => [], 'is_public' => true, 'label' => 'Email Addresses'],
                'whatsapp' => ['type' => 'string', 'default' => '', 'is_public' => true, 'label' => 'WhatsApp Number'],
                'telegram' => ['type' => 'string', 'default' => '', 'is_public' => true, 'label' => 'Telegram'],
            ],
            'social' => [
                'facebook' => ['type' => 'string', 'default' => '', 'is_public' => true, 'label' => 'Facebook URL'],
                'twitter' => ['type' => 'string', 'default' => '', 'is_public' => true, 'label' => 'Twitter/X URL'],
                'instagram' => ['type' => 'string', 'default' => '', 'is_public' => true, 'label' => 'Instagram URL'],
                'youtube' => ['type' => 'string', 'default' => '', 'is_public' => true, 'label' => 'YouTube URL'],
                'linkedin' => ['type' => 'string', 'default' => '', 'is_public' => true, 'label' => 'LinkedIn URL'],
                'pinterest' => ['type' => 'string', 'default' => '', 'is_public' => true, 'label' => 'Pinterest URL'],
            ],
            'theme' => [
                'navbar_background' => ['type' => 'color', 'default' => '#ffffff', 'is_public' => true, 'label' => 'Navbar Background'],
                'navbar_text_color' => ['type' => 'color', 'default' => '#1e293b', 'is_public' => true, 'label' => 'Navbar Text Color'],
                'body_background' => ['type' => 'color', 'default' => '#f8fafc', 'is_public' => true, 'label' => 'Body Background'],
                'body_text_color' => ['type' => 'color', 'default' => '#334155', 'is_public' => true, 'label' => 'Body Text Color'],
                'footer_background' => ['type' => 'color', 'default' => '#1e293b', 'is_public' => true, 'label' => 'Footer Background'],
                'footer_text_color' => ['type' => 'color', 'default' => '#f1f5f9', 'is_public' => true, 'label' => 'Footer Text Color'],
            ],
            'mail' => [
                'smtp_host' => ['type' => 'string', 'default' => '', 'is_public' => false, 'label' => 'SMTP Host'],
                'smtp_port' => ['type' => 'integer', 'default' => 587, 'is_public' => false, 'label' => 'SMTP Port'],
                'smtp_username' => ['type' => 'string', 'default' => '', 'is_public' => false, 'label' => 'SMTP Username'],
                'smtp_password' => ['type' => 'string', 'default' => '', 'is_public' => false, 'is_encrypted' => true, 'label' => 'SMTP Password'],
                'smtp_encryption' => ['type' => 'string', 'default' => 'tls', 'is_public' => false, 'label' => 'SMTP Encryption'],
                'mail_from_address' => ['type' => 'string', 'default' => '', 'is_public' => false, 'label' => 'From Address'],
                'mail_from_name' => ['type' => 'string', 'default' => '', 'is_public' => false, 'label' => 'From Name'],
            ],
            'advanced' => [
                'custom_css' => ['type' => 'css', 'default' => '', 'is_public' => true, 'label' => 'Custom CSS'],
            ],
            'recaptcha' => [
                'recaptcha_enabled' => ['type' => 'boolean', 'default' => false, 'is_public' => false, 'label' => 'Enable reCAPTCHA'],
                'recaptcha_site_key' => ['type' => 'string', 'default' => '', 'is_public' => true, 'label' => 'Site Key'],
                'recaptcha_secret_key' => ['type' => 'string', 'default' => '', 'is_public' => false, 'is_encrypted' => true, 'label' => 'Secret Key'],
                'recaptcha_version' => ['type' => 'string', 'default' => 'v2', 'is_public' => true, 'label' => 'reCAPTCHA Version'],
            ],
        ];
    }

    /**
     * Get all settings formatted for the admin UI.
     */
    public function getAllForAdmin(): array
    {
        $definitions = self::getSettingDefinitions();
        $settings = [];

        foreach ($definitions as $group => $keys) {
            $settings[$group] = [];
            foreach ($keys as $key => $meta) {
                $settings[$group][$key] = Setting::getValue($group, $key, $meta['default']);
            }
        }

        // Add media URLs
        $settings['media'] = $this->getMediaUrls();

        return $settings;
    }

    /**
     * Get media URLs for settings that use media library.
     */
    public function getMediaUrls(): array
    {
        $mediaSetting = Setting::where('group', 'site')
            ->where('key', 'site_name')
            ->first();

        if (! $mediaSetting) {
            return [
                'logo_url' => null,
                'favicon_url' => null,
                'banner_url' => null,
                'watermark_url' => $this->getWatermarkUrl(),
            ];
        }

        return [
            'logo_url' => $mediaSetting->getFirstMediaUrl('logo') ?: null,
            'favicon_url' => $mediaSetting->getFirstMediaUrl('favicon') ?: null,
            'banner_url' => $mediaSetting->getFirstMediaUrl('banner') ?: null,
            'watermark_url' => $this->getWatermarkUrl(),
        ];
    }

    protected function getWatermarkUrl(): ?string
    {
        $path = public_path('watermark.png');

        return file_exists($path) ? asset('watermark.png') : null;
    }

    /**
     * Update settings from request data.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateFromRequest(array $data, Request $request): void
    {
        $definitions = self::getSettingDefinitions();

        foreach ($definitions as $group => $keys) {
            foreach ($keys as $key => $meta) {
                // Check if this key exists in the data
                if (! array_key_exists($key, $data)) {
                    // For group-scoped data like 'site.site_name'
                    if (isset($data[$group][$key])) {
                        $value = $data[$group][$key];
                    } else {
                        continue;
                    }
                } else {
                    $value = $data[$key];
                }

                // Special handling for encrypted fields (don't overwrite if empty)
                if (($meta['is_encrypted'] ?? false) && empty($value)) {
                    continue;
                }

                Setting::setValue($group, $key, $value, [
                    'type' => $meta['type'],
                    'is_encrypted' => $meta['is_encrypted'] ?? false,
                    'is_public' => $meta['is_public'] ?? false,
                    'label' => $meta['label'] ?? null,
                ]);
            }
        }

        // Handle media uploads
        $this->handleMediaUploads($request);
    }

    /**
     * Handle media file uploads.
     */
    protected function handleMediaUploads(Request $request): void
    {
        $mediaSetting = Setting::firstOrCreate(
            ['group' => 'site', 'key' => 'site_name', 'context' => 'global'],
            ['type' => 'string', 'value' => 'TriptonicBD']
        );

        $mediaFields = ['logo', 'favicon', 'banner'];

        foreach ($mediaFields as $field) {
            if ($request->hasFile($field)) {
                $mediaSetting->clearMediaCollection($field);
                $mediaSetting->addMediaFromRequest($field)->toMediaCollection($field);
            } elseif ($request->boolean("remove_previous_{$field}")) {
                $mediaSetting->clearMediaCollection($field);
            }
        }

        // Handle watermark (stays in public directory)
        if ($request->hasFile('watermark')) {
            $watermarkFile = $request->file('watermark');
            $watermarkFile->move(public_path(), 'watermark.png');
        } elseif ($request->boolean('remove_previous_watermark')) {
            $watermarkPath = public_path('watermark.png');
            if (file_exists($watermarkPath)) {
                unlink($watermarkPath);
            }
        }
    }

    /**
     * Get SMTP configuration for mail sending.
     */
    public function getSmtpConfig(): array
    {
        return [
            'host' => Setting::getValue('mail', 'smtp_host', ''),
            'port' => Setting::getValue('mail', 'smtp_port', 587),
            'username' => Setting::getValue('mail', 'smtp_username', ''),
            'password' => Setting::getValue('mail', 'smtp_password', ''),
            'encryption' => Setting::getValue('mail', 'smtp_encryption', 'tls'),
            'from_address' => Setting::getValue('mail', 'mail_from_address', ''),
            'from_name' => Setting::getValue('mail', 'mail_from_name', ''),
        ];
    }

    /**
     * Seed default settings.
     */
    public static function seedDefaults(): void
    {
        $definitions = self::getSettingDefinitions();

        foreach ($definitions as $group => $keys) {
            foreach ($keys as $key => $meta) {
                Setting::setValue($group, $key, $meta['default'], [
                    'type' => $meta['type'],
                    'is_encrypted' => $meta['is_encrypted'] ?? false,
                    'is_public' => $meta['is_public'] ?? false,
                    'label' => $meta['label'] ?? null,
                ]);
            }
        }
    }

    /**
     * Get all public settings as a flat object for Blade templates.
     * Returns an object with properties like $settings->site_name, $settings->facebook, etc.
     */
    public function getPublicSettingsObject(): object
    {
        $definitions = self::getSettingDefinitions();
        $settings = [];

        foreach ($definitions as $group => $keys) {
            foreach ($keys as $key => $meta) {
                if ($meta['is_public'] ?? false) {
                    $settings[$key] = Setting::getValue($group, $key, $meta['default']);
                }
            }
        }

        // Add media URLs
        $media = $this->getMediaUrls();
        $settings['logo_url'] = $media['logo_url'];
        $settings['favicon_url'] = $media['favicon_url'];
        $settings['banner_url'] = $media['banner_url'];
        $settings['watermark_url'] = $media['watermark_url'];

        return (object) $settings;
    }
}
