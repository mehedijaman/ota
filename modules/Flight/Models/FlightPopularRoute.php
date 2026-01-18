<?php

namespace Modules\Flight\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Flight\Database\Factories\FlightPopularRouteFactory;
use Modules\Support\Models\BaseModel;

class FlightPopularRoute extends BaseModel
{
    use HasFactory;

    protected $table = 'flight_popular_routes';

    protected function casts(): array
    {
        return [
            'search_count' => 'integer',
            'booking_count' => 'integer',
            'conversion_rate' => 'decimal:2',
            'avg_price' => 'decimal:2',
            'min_price' => 'decimal:2',
            'max_price' => 'decimal:2',
            'rank' => 'integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'avg_duration_minutes' => 'integer',
            'direct_flight_duration' => 'integer',
            'last_calculated_at' => 'datetime',
        ];
    }

    protected static function newFactory(): FlightPopularRouteFactory
    {
        return FlightPopularRouteFactory::new();
    }

    // Relationships
    public function originAirport(): BelongsTo
    {
        return $this->belongsTo(FlightAirport::class, 'origin_airport_id');
    }

    public function destinationAirport(): BelongsTo
    {
        return $this->belongsTo(FlightAirport::class, 'destination_airport_id');
    }

    // Accessors
    public function getRouteDisplayAttribute(): string
    {
        return "{$this->origin_code} → {$this->destination_code}";
    }

    public function getFullRouteDisplayAttribute(): string
    {
        $origin = $this->originAirport?->city ?? $this->origin_code;
        $destination = $this->destinationAirport?->city ?? $this->destination_code;

        return "{$origin} to {$destination}";
    }

    public function getPriceRangeDisplayAttribute(): string
    {
        if (! $this->min_price || ! $this->max_price) {
            return 'N/A';
        }

        return number_format($this->min_price, 0).' - '.number_format($this->max_price, 0).' '.$this->currency_code;
    }

    public function getMinPriceDisplayAttribute(): string
    {
        if (! $this->min_price) {
            return 'N/A';
        }

        return 'From '.number_format($this->min_price, 0).' '.$this->currency_code;
    }

    public function getAvgDurationDisplayAttribute(): string
    {
        if (! $this->avg_duration_minutes) {
            return 'N/A';
        }

        $hours = floor($this->avg_duration_minutes / 60);
        $minutes = $this->avg_duration_minutes % 60;

        return "{$hours}h {$minutes}m";
    }

    public function getConversionRateDisplayAttribute(): string
    {
        return number_format($this->conversion_rate, 1).'%';
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

    public function scopeByRank($query)
    {
        return $query->orderBy('rank');
    }

    public function scopeByBookings($query)
    {
        return $query->orderByDesc('booking_count');
    }

    public function scopeBySearches($query)
    {
        return $query->orderByDesc('search_count');
    }

    public function scopeFromOrigin($query, int $airportId)
    {
        return $query->where('origin_airport_id', $airportId);
    }

    public function scopeToDestination($query, int $airportId)
    {
        return $query->where('destination_airport_id', $airportId);
    }

    // Methods
    public function incrementSearchCount(): void
    {
        $this->increment('search_count');
        $this->updateConversionRate();
    }

    public function incrementBookingCount(): void
    {
        $this->increment('booking_count');
        $this->updateConversionRate();
    }

    public function updateConversionRate(): void
    {
        if ($this->search_count > 0) {
            $this->update([
                'conversion_rate' => round($this->booking_count / $this->search_count * 100, 2),
            ]);
        }
    }

    public function updatePricing(float $minPrice, float $maxPrice, float $avgPrice): void
    {
        $this->update([
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'avg_price' => $avgPrice,
            'last_calculated_at' => now(),
        ]);
    }
}
