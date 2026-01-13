<?php

namespace Modules\Visa\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;
use Modules\Visa\Database\Factories\VisaCategoryFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class VisaCategory extends BaseModel implements HasMedia
{
    use ActivityLog, HasFactory, InteractsWithMedia, Searchable, SoftDeletes;

    protected $table = 'visa_categories';

    protected static function newFactory(): VisaCategoryFactory
    {
        return VisaCategoryFactory::new();
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get visa types in this category.
     */
    public function visaTypes(): HasMany
    {
        return $this->hasMany(VisaType::class, 'category_id');
    }

    /**
     * Get visas in this category.
     */
    public function visas(): HasMany
    {
        return $this->hasMany(Visa::class, 'category_id');
    }

    /**
     * Scope active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get icon image URL.
     */
    public function getIconUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('icon') ?: null;
    }

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('icon')->singleFile();
    }
}
