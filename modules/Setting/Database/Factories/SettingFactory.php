<?php

namespace Modules\Setting\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Setting\Models\Setting;

class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid()->toString(),
            'group' => 'site',
            'key' => fake()->unique()->slug(2),
            'value' => fake()->sentence(),
            'type' => 'string',
            'context' => 'global',
            'is_encrypted' => false,
            'is_public' => true,
            'is_active' => true,
            'label' => fake()->words(2, true),
            'description' => null,
        ];
    }

    /**
     * Site identity settings.
     */
    public function siteName(string $name = 'TriptonicBD'): static
    {
        return $this->state(fn () => [
            'group' => 'site',
            'key' => 'site_name',
            'value' => $name,
            'type' => 'string',
            'is_public' => true,
            'label' => 'Site Name',
        ]);
    }

    public function siteDescription(string $description = ''): static
    {
        return $this->state(fn () => [
            'group' => 'site',
            'key' => 'description',
            'value' => $description,
            'type' => 'string',
            'is_public' => true,
            'label' => 'Site Description',
        ]);
    }

    public function slogan(string $slogan = 'Your Travel Partner'): static
    {
        return $this->state(fn () => [
            'group' => 'site',
            'key' => 'slogan',
            'value' => $slogan,
            'type' => 'string',
            'is_public' => true,
            'label' => 'Slogan',
        ]);
    }

    public function showName(bool $show = true): static
    {
        return $this->state(fn () => [
            'group' => 'site',
            'key' => 'show_name',
            'value' => $show ? '1' : '0',
            'type' => 'boolean',
            'is_public' => true,
            'label' => 'Show Site Name',
        ]);
    }

    public function showSlogan(bool $show = true): static
    {
        return $this->state(fn () => [
            'group' => 'site',
            'key' => 'show_slogan',
            'value' => $show ? '1' : '0',
            'type' => 'boolean',
            'is_public' => true,
            'label' => 'Show Slogan',
        ]);
    }

    public function showLogo(bool $show = true): static
    {
        return $this->state(fn () => [
            'group' => 'site',
            'key' => 'show_logo',
            'value' => $show ? '1' : '0',
            'type' => 'boolean',
            'is_public' => true,
            'label' => 'Show Logo',
        ]);
    }

    /**
     * Contact settings.
     */
    public function addresses(array $addresses = []): static
    {
        $default = [
            ['label' => 'Head Office', 'address' => '123 Main Street, Dhaka, Bangladesh'],
        ];

        return $this->state(fn () => [
            'group' => 'contact',
            'key' => 'addresses',
            'value' => json_encode($addresses ?: $default),
            'type' => 'json',
            'is_public' => true,
            'label' => 'Addresses',
        ]);
    }

    public function phones(array $phones = []): static
    {
        $default = [
            ['label' => 'Main', 'number' => '+880 1234-567890'],
        ];

        return $this->state(fn () => [
            'group' => 'contact',
            'key' => 'phones',
            'value' => json_encode($phones ?: $default),
            'type' => 'json',
            'is_public' => true,
            'label' => 'Phone Numbers',
        ]);
    }

    public function emails(array $emails = []): static
    {
        $default = [
            ['label' => 'General', 'email' => 'info@triptonicbd.com'],
        ];

        return $this->state(fn () => [
            'group' => 'contact',
            'key' => 'emails',
            'value' => json_encode($emails ?: $default),
            'type' => 'json',
            'is_public' => true,
            'label' => 'Email Addresses',
        ]);
    }

    public function whatsapp(string $number = ''): static
    {
        return $this->state(fn () => [
            'group' => 'contact',
            'key' => 'whatsapp',
            'value' => $number,
            'type' => 'string',
            'is_public' => true,
            'label' => 'WhatsApp Number',
        ]);
    }

    public function googleMap(string $embed = ''): static
    {
        return $this->state(fn () => [
            'group' => 'contact',
            'key' => 'google_map',
            'value' => $embed,
            'type' => 'string',
            'is_public' => true,
            'label' => 'Google Map Embed',
        ]);
    }

    /**
     * Social media settings.
     */
    public function facebook(string $url = ''): static
    {
        return $this->state(fn () => [
            'group' => 'social',
            'key' => 'facebook',
            'value' => $url,
            'type' => 'string',
            'is_public' => true,
            'label' => 'Facebook URL',
        ]);
    }

    public function twitter(string $url = ''): static
    {
        return $this->state(fn () => [
            'group' => 'social',
            'key' => 'twitter',
            'value' => $url,
            'type' => 'string',
            'is_public' => true,
            'label' => 'Twitter/X URL',
        ]);
    }

    public function instagram(string $url = ''): static
    {
        return $this->state(fn () => [
            'group' => 'social',
            'key' => 'instagram',
            'value' => $url,
            'type' => 'string',
            'is_public' => true,
            'label' => 'Instagram URL',
        ]);
    }

    public function youtube(string $url = ''): static
    {
        return $this->state(fn () => [
            'group' => 'social',
            'key' => 'youtube',
            'value' => $url,
            'type' => 'string',
            'is_public' => true,
            'label' => 'YouTube URL',
        ]);
    }

    public function linkedin(string $url = ''): static
    {
        return $this->state(fn () => [
            'group' => 'social',
            'key' => 'linkedin',
            'value' => $url,
            'type' => 'string',
            'is_public' => true,
            'label' => 'LinkedIn URL',
        ]);
    }

    public function pinterest(string $url = ''): static
    {
        return $this->state(fn () => [
            'group' => 'social',
            'key' => 'pinterest',
            'value' => $url,
            'type' => 'string',
            'is_public' => true,
            'label' => 'Pinterest URL',
        ]);
    }

    /**
     * Theme settings.
     */
    public function navbarBackground(string $color = '#ffffff'): static
    {
        return $this->state(fn () => [
            'group' => 'theme',
            'key' => 'navbar_background',
            'value' => $color,
            'type' => 'color',
            'is_public' => true,
            'label' => 'Navbar Background',
        ]);
    }

    public function navbarTextColor(string $color = '#1e293b'): static
    {
        return $this->state(fn () => [
            'group' => 'theme',
            'key' => 'navbar_text_color',
            'value' => $color,
            'type' => 'color',
            'is_public' => true,
            'label' => 'Navbar Text Color',
        ]);
    }

    public function bodyBackground(string $color = '#f8fafc'): static
    {
        return $this->state(fn () => [
            'group' => 'theme',
            'key' => 'body_background',
            'value' => $color,
            'type' => 'color',
            'is_public' => true,
            'label' => 'Body Background',
        ]);
    }

    public function bodyTextColor(string $color = '#334155'): static
    {
        return $this->state(fn () => [
            'group' => 'theme',
            'key' => 'body_text_color',
            'value' => $color,
            'type' => 'color',
            'is_public' => true,
            'label' => 'Body Text Color',
        ]);
    }

    public function footerBackground(string $color = '#1e293b'): static
    {
        return $this->state(fn () => [
            'group' => 'theme',
            'key' => 'footer_background',
            'value' => $color,
            'type' => 'color',
            'is_public' => true,
            'label' => 'Footer Background',
        ]);
    }

    public function footerTextColor(string $color = '#f1f5f9'): static
    {
        return $this->state(fn () => [
            'group' => 'theme',
            'key' => 'footer_text_color',
            'value' => $color,
            'type' => 'color',
            'is_public' => true,
            'label' => 'Footer Text Color',
        ]);
    }

    /**
     * SMTP / Mail settings.
     */
    public function smtpHost(string $host = ''): static
    {
        return $this->state(fn () => [
            'group' => 'mail',
            'key' => 'smtp_host',
            'value' => $host,
            'type' => 'string',
            'is_public' => false,
            'label' => 'SMTP Host',
        ]);
    }

    public function smtpPort(int $port = 587): static
    {
        return $this->state(fn () => [
            'group' => 'mail',
            'key' => 'smtp_port',
            'value' => (string) $port,
            'type' => 'integer',
            'is_public' => false,
            'label' => 'SMTP Port',
        ]);
    }

    public function smtpUsername(string $username = ''): static
    {
        return $this->state(fn () => [
            'group' => 'mail',
            'key' => 'smtp_username',
            'value' => $username,
            'type' => 'string',
            'is_public' => false,
            'label' => 'SMTP Username',
        ]);
    }

    public function smtpPassword(string $password = ''): static
    {
        return $this->state(fn () => [
            'group' => 'mail',
            'key' => 'smtp_password',
            'value' => $password ? encrypt($password) : '',
            'type' => 'string',
            'is_public' => false,
            'is_encrypted' => true,
            'label' => 'SMTP Password',
        ]);
    }

    public function smtpEncryption(string $encryption = 'tls'): static
    {
        return $this->state(fn () => [
            'group' => 'mail',
            'key' => 'smtp_encryption',
            'value' => $encryption,
            'type' => 'string',
            'is_public' => false,
            'label' => 'SMTP Encryption',
        ]);
    }

    public function mailFromAddress(string $address = ''): static
    {
        return $this->state(fn () => [
            'group' => 'mail',
            'key' => 'mail_from_address',
            'value' => $address,
            'type' => 'string',
            'is_public' => false,
            'label' => 'From Address',
        ]);
    }

    public function mailFromName(string $name = ''): static
    {
        return $this->state(fn () => [
            'group' => 'mail',
            'key' => 'mail_from_name',
            'value' => $name,
            'type' => 'string',
            'is_public' => false,
            'label' => 'From Name',
        ]);
    }

    /**
     * Advanced settings.
     */
    public function customCss(string $css = ''): static
    {
        return $this->state(fn () => [
            'group' => 'advanced',
            'key' => 'custom_css',
            'value' => $css,
            'type' => 'css',
            'is_public' => true,
            'label' => 'Custom CSS',
        ]);
    }

    /**
     * Create all default settings at once.
     */
    public static function createDefaults(): void
    {
        $factory = new self;

        // Site settings
        $factory->siteName()->create();
        $factory->siteDescription()->create();
        $factory->slogan()->create();
        $factory->showName()->create();
        $factory->showSlogan()->create();
        $factory->showLogo()->create();

        // Contact settings
        $factory->addresses()->create();
        $factory->phones()->create();
        $factory->emails()->create();
        $factory->whatsapp()->create();
        $factory->googleMap()->create();

        // Social settings
        $factory->facebook()->create();
        $factory->twitter()->create();
        $factory->instagram()->create();
        $factory->youtube()->create();
        $factory->linkedin()->create();
        $factory->pinterest()->create();

        // Theme settings
        $factory->navbarBackground()->create();
        $factory->navbarTextColor()->create();
        $factory->bodyBackground()->create();
        $factory->bodyTextColor()->create();
        $factory->footerBackground()->create();
        $factory->footerTextColor()->create();

        // Mail settings
        $factory->smtpHost()->create();
        $factory->smtpPort()->create();
        $factory->smtpUsername()->create();
        $factory->smtpPassword()->create();
        $factory->smtpEncryption()->create();
        $factory->mailFromAddress()->create();
        $factory->mailFromName()->create();

        // Advanced settings
        $factory->customCss()->create();
    }
}
