<?php

namespace Modules\Tour\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;
use Modules\Tour\Database\Factories\TourDestinationFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TourDestination extends BaseModel implements HasMedia
{
    use ActivityLog, HasFactory, InteractsWithMedia, Searchable, SoftDeletes;

    protected static function newFactory(): TourDestinationFactory
    {
        return TourDestinationFactory::new();
    }

    protected $table = 'tour_destinations';

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get tours at this destination.
     */
    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class, 'destination_id');
    }

    /**
     * Scope active destinations.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope featured destinations.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
        $this->addMediaCollection('gallery');
    }
}
