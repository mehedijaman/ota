<?php

namespace Modules\Tour\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;
use Modules\Tour\Database\Factories\TourFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Tour extends BaseModel implements HasMedia
{
    use ActivityLog, HasFactory, InteractsWithMedia, Searchable, SoftDeletes;

    protected $table = 'tours';

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'child_price' => 'decimal:2',
            'infant_price' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'languages' => 'array',
            'age_restrictions' => 'array',
            'is_featured' => 'boolean',
            'is_bestseller' => 'boolean',
            'is_active' => 'boolean',
            'valid_from' => 'date',
            'valid_to' => 'date',
        ];
    }

    /**
     * Get the factory for the model.
     */
    protected static function newFactory(): TourFactory
    {
        return TourFactory::new();
    }

    /**
     * Get the category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TourCategory::class, 'category_id');
    }

    /**
     * Get the destination.
     */
    public function destination(): BelongsTo
    {
        return $this->belongsTo(TourDestination::class, 'destination_id');
    }

    /**
     * Get itineraries.
     */
    public function itineraries(): HasMany
    {
        return $this->hasMany(TourItinerary::class)->orderBy('day_number');
    }

    /**
     * Get inclusions.
     */
    public function inclusions(): HasMany
    {
        return $this->hasMany(TourInclusion::class)->where('type', 'inclusion')->orderBy('sort_order');
    }

    /**
     * Get exclusions.
     */
    public function exclusions(): HasMany
    {
        return $this->hasMany(TourInclusion::class)->where('type', 'exclusion')->orderBy('sort_order');
    }

    /**
     * Get all inclusion items (both types).
     */
    public function allInclusions(): HasMany
    {
        return $this->hasMany(TourInclusion::class)->orderBy('sort_order');
    }

    /**
     * Get pricings.
     */
    public function pricings(): HasMany
    {
        return $this->hasMany(TourPricing::class);
    }

    /**
     * Get reviews.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(TourReview::class);
    }

    /**
     * Get approved reviews.
     */
    public function approvedReviews(): HasMany
    {
        return $this->reviews()->approved();
    }

    /**
     * Get FAQs.
     */
    public function faqs(): HasMany
    {
        return $this->hasMany(TourFaq::class)->orderBy('sort_order');
    }

    /**
     * Get highlights.
     */
    public function highlights(): HasMany
    {
        return $this->hasMany(TourHighlight::class)->orderBy('sort_order');
    }

    /**
     * Get extras.
     */
    public function extras(): HasMany
    {
        return $this->hasMany(TourExtra::class);
    }

    /**
     * Get bookings.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(TourBooking::class);
    }

    /**
     * Get cancellation policies.
     */
    public function cancellationPolicies(): HasMany
    {
        return $this->hasMany(TourCancellationPolicy::class)->orderByDesc('days_before_departure');
    }

    /**
     * Get average rating.
     */
    public function getAverageRatingAttribute(): float
    {
        return round($this->approvedReviews()->avg('rating') ?? 0, 1);
    }

    /**
     * Get reviews count.
     */
    public function getReviewsCountAttribute(): int
    {
        return $this->approvedReviews()->count();
    }

    /**
     * Get effective price (discount or base).
     */
    public function getEffectivePriceAttribute(): float
    {
        return $this->discount_price ?? $this->base_price;
    }

    /**
     * Get duration string.
     */
    public function getDurationAttribute(): string
    {
        $parts = [];
        if ($this->duration_days > 0) {
            $parts[] = $this->duration_days.' '.($this->duration_days === 1 ? 'Day' : 'Days');
        }
        if ($this->duration_nights > 0) {
            $parts[] = $this->duration_nights.' '.($this->duration_nights === 1 ? 'Night' : 'Nights');
        }

        return implode(', ', $parts);
    }

    /**
     * Scope active tours.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope featured tours.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope bestseller tours.
     */
    public function scopeBestseller($query)
    {
        return $query->where('is_bestseller', true);
    }

    /**
     * Get thumbnail URL attribute.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('thumbnail') ?: null;
    }

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')->singleFile();
        $this->addMediaCollection('gallery');
    }
}
