<?php

namespace Modules\Hotel\Models;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Hotel\Database\Factories\HotelFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Hotel extends BaseModel implements HasMedia
{
    use HasFactory, HasUuid, InteractsWithMedia, Searchable, SoftDeletes;

    protected $table = 'hotels';

    protected function casts(): array
    {
        return [
            'star_rating' => 'integer',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'check_in_time' => 'datetime:H:i',
            'check_out_time' => 'datetime:H:i',
            'check_in_time_end' => 'datetime:H:i',
            'total_rooms' => 'integer',
            'total_floors' => 'integer',
            'policies' => 'array',
            'child_policies' => 'array',
            'pet_policies' => 'array',
            'accepts_credit_cards' => 'boolean',
            'accepted_cards' => 'array',
            'is_featured' => 'boolean',
            'is_recommended' => 'boolean',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
            'is_instant_bookable' => 'boolean',
            'avg_rating' => 'decimal:2',
            'reviews_count' => 'integer',
            'bookings_count' => 'integer',
            'commission_rate' => 'decimal:2',
            'markup_rate' => 'decimal:2',
        ];
    }

    protected static function newFactory(): HotelFactory
    {
        return HotelFactory::new();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')->singleFile();
        $this->addMediaCollection('gallery');
        $this->addMediaCollection('logo')->singleFile();
    }

    // Relationships
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(HotelSupplier::class, 'supplier_id');
    }

    public function chain(): BelongsTo
    {
        return $this->belongsTo(HotelChain::class, 'chain_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(\Nnjeim\World\Models\Country::class, 'country_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(HotelCity::class, 'city_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(HotelArea::class, 'area_id');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(HotelRoom::class, 'hotel_id');
    }

    public function activeRooms(): HasMany
    {
        return $this->rooms()->where('is_active', true)->orderBy('sort_order');
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(HotelAmenity::class, 'hotel_amenity_hotel', 'hotel_id', 'amenity_id')
            ->withPivot('is_free', 'fee', 'fee_type', 'notes')
            ->withTimestamps();
    }

    public function ratePlans(): HasMany
    {
        return $this->hasMany(HotelRatePlan::class, 'hotel_id');
    }

    public function cancellationPolicies(): HasMany
    {
        return $this->hasMany(HotelCancellationPolicy::class, 'hotel_id');
    }

    public function defaultCancellationPolicy()
    {
        return $this->cancellationPolicies()->where('is_default', true)->first();
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(HotelBooking::class, 'hotel_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(HotelReview::class, 'hotel_id');
    }

    public function publishedReviews(): HasMany
    {
        return $this->reviews()->where('is_published', true);
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(HotelFaq::class, 'hotel_id')->orderBy('sort_order');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Accessors
    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('thumbnail') ?: null;
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->address_line_2,
            $this->area?->name,
            $this->city?->name,
            $this->postal_code,
            $this->country?->name,
        ]);

        return implode(', ', $parts);
    }

    public function getLocationAttribute(): string
    {
        return $this->city?->name.', '.$this->country?->name;
    }

    public function getStarRatingDisplayAttribute(): string
    {
        return str_repeat('★', $this->star_rating ?? 0);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeRecommended($query)
    {
        return $query->where('is_recommended', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeInCity($query, int $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    public function scopeInCountry($query, int $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    public function scopeWithStars($query, int $stars)
    {
        return $query->where('star_rating', $stars);
    }

    public function scopeMinStars($query, int $minStars)
    {
        return $query->where('star_rating', '>=', $minStars);
    }

    public function scopeNearby($query, float $lat, float $lng, float $radiusKm = 10)
    {
        return $query->selectRaw('*, 
            (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
            [$lat, $lng, $lat])
            ->having('distance', '<', $radiusKm)
            ->orderBy('distance');
    }

    // Methods
    public function updateReviewStats(): void
    {
        $this->update([
            'avg_rating' => round($this->publishedReviews()->avg('overall_rating') ?? 0, 2),
            'reviews_count' => $this->publishedReviews()->count(),
        ]);
    }

    public function incrementBookingsCount(): void
    {
        $this->increment('bookings_count');
    }

    public function isManualInventory(): bool
    {
        return is_null($this->supplier_id);
    }
}
