<?php

namespace Modules\Visa\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;
use Modules\Visa\Database\Factories\VisaCountryFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class VisaCountry extends BaseModel implements HasMedia
{
    use ActivityLog, HasFactory, InteractsWithMedia, Searchable, SoftDeletes;

    protected $table = 'visa_countries';

    protected static function newFactory(): VisaCountryFactory
    {
        return VisaCountryFactory::new();
    }

    protected function casts(): array
    {
        return [
            'embassy_working_hours' => 'array',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get visa types for this country.
     */
    public function visaTypes(): HasMany
    {
        return $this->hasMany(VisaType::class, 'country_id');
    }

    /**
     * Get visas for this country.
     */
    public function visas(): HasMany
    {
        return $this->hasMany(Visa::class, 'country_id');
    }

    /**
     * Get applications for this country.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(VisaApplication::class, 'country_id');
    }

    /**
     * Scope active countries.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope popular countries.
     */
    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    /**
     * Get flag image URL.
     */
    public function getFlagUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('flag') ?: null;
    }

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('flag')->singleFile();
        $this->addMediaCollection('images');
    }
}
