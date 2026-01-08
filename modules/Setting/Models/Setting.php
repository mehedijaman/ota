<?php

namespace Modules\Setting\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Setting\Database\Factories\SettingFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Setting extends BaseModel implements HasMedia
{
    use ActivityLog, HasFactory, InteractsWithMedia;

    protected $table = 'settings';

    protected $appends = ['logo_url', 'favicon_url', 'banner_url', 'watermark_url'];

    protected function casts(): array
    {
        return [
            // JSON fields
            'addresses' => 'array',
            'phones' => 'array',
            'emails' => 'array',
            'smtp_settings' => 'array',

            // Display options
            'show_name' => 'boolean',
            'show_slogan' => 'boolean',
            'show_logo' => 'boolean',
        ];
    }

    protected static function newFactory(): Factory
    {
        return SettingFactory::new();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
        $this->addMediaCollection('favicon')->singleFile();
        $this->addMediaCollection('banner')->singleFile();
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('logo') ?: null;
    }

    public function getFaviconUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('favicon') ?: null;
    }

    public function getBannerUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('banner') ?: null;
    }

    public function getWatermarkUrlAttribute(): ?string
    {
        $path = public_path('watermark.png');

        if (file_exists($path)) {
            return asset('watermark.png');
        }

        return null;
    }
}
