<?php

namespace Modules\Visa\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;
use Modules\Visa\Database\Factories\VisaFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Visa extends BaseModel implements HasMedia
{
    use ActivityLog, HasFactory, InteractsWithMedia, Searchable, SoftDeletes;

    protected $table = 'visas';

    protected static function newFactory(): VisaFactory
    {
        return VisaFactory::new();
    }

    protected function casts(): array
    {
        return [
            'included_services' => 'array',
            'excluded_services' => 'array',
            'base_price' => 'decimal:2',
            'service_fee' => 'decimal:2',
            'embassy_fee' => 'decimal:2',
            'vfs_fee' => 'decimal:2',
            'success_rate' => 'decimal:2',
            'is_express_available' => 'boolean',
            'is_interview_required' => 'boolean',
            'is_biometric_required' => 'boolean',
            'is_appointment_required' => 'boolean',
            'is_travel_insurance_required' => 'boolean',
            'is_bank_statement_required' => 'boolean',
            'is_invitation_letter_required' => 'boolean',
            'is_hotel_booking_required' => 'boolean',
            'is_flight_booking_required' => 'boolean',
            'is_featured' => 'boolean',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
            'valid_from' => 'date',
            'valid_to' => 'date',
        ];
    }

    /**
     * Get the country.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(VisaCountry::class, 'country_id');
    }

    /**
     * Get the category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(VisaCategory::class, 'category_id');
    }

    /**
     * Get the visa type.
     */
    public function visaType(): BelongsTo
    {
        return $this->belongsTo(VisaType::class, 'visa_type_id');
    }

    /**
     * Get requirements.
     */
    public function requirements(): HasMany
    {
        return $this->hasMany(VisaRequirement::class)->orderBy('sort_order');
    }

    /**
     * Get mandatory requirements.
     */
    public function mandatoryRequirements(): HasMany
    {
        return $this->requirements()->where('is_mandatory', true);
    }

    /**
     * Get fees.
     */
    public function fees(): HasMany
    {
        return $this->hasMany(VisaFee::class)->orderBy('sort_order');
    }

    /**
     * Get processing times.
     */
    public function processingTimes(): HasMany
    {
        return $this->hasMany(VisaProcessingTime::class)->orderBy('sort_order');
    }

    /**
     * Get FAQs.
     */
    public function faqs(): HasMany
    {
        return $this->hasMany(VisaFaq::class)->orderBy('sort_order');
    }

    /**
     * Get checklists.
     */
    public function checklists(): HasMany
    {
        return $this->hasMany(VisaChecklist::class)->orderBy('sort_order');
    }

    /**
     * Get applications.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(VisaApplication::class);
    }

    /**
     * Get reviews.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(VisaReview::class);
    }

    /**
     * Get approved reviews.
     */
    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('status', 'approved');
    }

    /**
     * Scope active visas.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope featured visas.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope popular visas.
     */
    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    /**
     * Get total price attribute.
     */
    public function getTotalPriceAttribute(): float
    {
        return $this->base_price + $this->service_fee + $this->embassy_fee + $this->vfs_fee;
    }

    /**
     * Get processing time range attribute.
     */
    public function getProcessingTimeRangeAttribute(): string
    {
        if ($this->processing_days_min && $this->processing_days_max) {
            if ($this->processing_days_min === $this->processing_days_max) {
                return $this->processing_days_min.' days';
            }

            return $this->processing_days_min.'-'.$this->processing_days_max.' days';
        }

        return 'N/A';
    }

    /**
     * Get average rating attribute.
     */
    public function getAverageRatingAttribute(): float
    {
        return round($this->approvedReviews()->avg('rating') ?? 0, 1);
    }

    /**
     * Get image URL attribute.
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('image') ?: null;
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
