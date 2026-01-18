<?php

namespace Modules\Flight\Models;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Flight\Database\Factories\FlightAirlineFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\Searchable;
use Nnjeim\World\Models\Country;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class FlightAirline extends BaseModel implements HasMedia
{
    use HasFactory, HasUuid, InteractsWithMedia, Searchable, SoftDeletes;

    protected $table = 'flight_airlines';

    protected function casts(): array
    {
        return [
            'founded_year' => 'integer',
            'fleet_size' => 'integer',
            'destinations_count' => 'integer',
            'baggage_policy' => 'array',
            'cancellation_policy' => 'array',
            'change_policy' => 'array',
            'allows_web_checkin' => 'boolean',
            'checkin_hours_before' => 'integer',
            'checkin_minutes_before' => 'integer',
            'default_commission_rate' => 'decimal:2',
            'default_markup_rate' => 'decimal:2',
            'is_preferred_partner' => 'boolean',
            'has_direct_contract' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'avg_rating' => 'decimal:2',
            'reviews_count' => 'integer',
        ];
    }

    protected static function newFactory(): FlightAirlineFactory
    {
        return FlightAirlineFactory::new();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
        $this->addMediaCollection('logo_small')->singleFile();
    }

    // Relationships
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function hubAirport(): BelongsTo
    {
        return $this->belongsTo(FlightAirport::class, 'hub_airport_id');
    }

    public function flights(): HasMany
    {
        return $this->hasMany(Flight::class, 'airline_id');
    }

    public function operatingFlights(): HasMany
    {
        return $this->hasMany(Flight::class, 'operating_airline_id');
    }

    public function fareClasses(): HasMany
    {
        return $this->hasMany(FlightFareClass::class, 'airline_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(FlightReview::class, 'airline_id');
    }

    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('is_approved', true);
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(FlightFaq::class, 'airline_id')->orderBy('sort_order');
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
    public function getLogoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('logo') ?: $this->logo;
    }

    public function getLogoSmallUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('logo_small') ?: $this->logo_small;
    }

    public function getDisplayNameAttribute(): string
    {
        return "{$this->name} ({$this->iata_code})";
    }

    public function getAllianceDisplayAttribute(): string
    {
        return match ($this->alliance) {
            'star_alliance' => 'Star Alliance',
            'oneworld' => 'Oneworld',
            'skyteam' => 'SkyTeam',
            default => 'Independent',
        };
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

    public function scopePreferredPartner($query)
    {
        return $query->where('is_preferred_partner', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOfAlliance($query, string $alliance)
    {
        return $query->where('alliance', $alliance);
    }

    public function scopeByCode($query, string $code)
    {
        return $query->where('iata_code', strtoupper($code))
            ->orWhere('icao_code', strtoupper($code));
    }

    // Methods
    public function updateReviewStats(): void
    {
        $this->update([
            'avg_rating' => round($this->approvedReviews()->avg('overall_rating') ?? 0, 2),
            'reviews_count' => $this->approvedReviews()->count(),
        ]);
    }

    public function isLowCost(): bool
    {
        return $this->type === 'low_cost';
    }

    public function isInAlliance(): bool
    {
        return $this->alliance !== 'none';
    }
}
