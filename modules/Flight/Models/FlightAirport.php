<?php

namespace Modules\Flight\Models;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Flight\Database\Factories\FlightAirportFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\Searchable;
use Nnjeim\World\Models\Country;

class FlightAirport extends BaseModel
{
    use HasFactory, HasUuid, Searchable, SoftDeletes;

    protected $table = 'flight_airports';

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'utc_offset_minutes' => 'integer',
            'is_international' => 'boolean',
            'is_domestic' => 'boolean',
            'terminals_count' => 'integer',
            'is_active' => 'boolean',
            'is_hub' => 'boolean',
            'is_popular' => 'boolean',
            'annual_passengers' => 'integer',
            'search_priority' => 'integer',
        ];
    }

    protected static function newFactory(): FlightAirportFactory
    {
        return FlightAirportFactory::new();
    }

    // Relationships
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function departureFlights(): HasMany
    {
        return $this->hasMany(Flight::class, 'departure_airport_id');
    }

    public function arrivalFlights(): HasMany
    {
        return $this->hasMany(Flight::class, 'arrival_airport_id');
    }

    public function hubAirlines(): HasMany
    {
        return $this->hasMany(FlightAirline::class, 'hub_airport_id');
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
    public function getDisplayNameAttribute(): string
    {
        return "{$this->city} ({$this->iata_code}) - {$this->name}";
    }

    public function getShortDisplayAttribute(): string
    {
        return "{$this->city} ({$this->iata_code})";
    }

    public function getFullLocationAttribute(): string
    {
        $parts = array_filter([$this->city, $this->state, $this->country?->name]);

        return implode(', ', $parts);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInternational($query)
    {
        return $query->where('is_international', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeHub($query)
    {
        return $query->where('is_hub', true);
    }

    public function scopeInCountry($query, int $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    public function scopeByCity($query, string $city)
    {
        return $query->where('city', 'like', "%{$city}%");
    }

    public function scopeByCode($query, string $code)
    {
        return $query->where('iata_code', strtoupper($code))
            ->orWhere('icao_code', strtoupper($code));
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('city', 'like', "%{$term}%")
                ->orWhere('iata_code', 'like', "%{$term}%")
                ->orWhere('icao_code', 'like', "%{$term}%")
                ->orWhere('search_keywords', 'like', "%{$term}%");
        });
    }

    public function scopeNearby($query, float $lat, float $lng, float $radiusKm = 100)
    {
        return $query->selectRaw('*, 
            (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
            [$lat, $lng, $lat])
            ->having('distance', '<', $radiusKm)
            ->orderBy('distance');
    }

    public function scopeByPriority($query)
    {
        return $query->orderByDesc('search_priority');
    }
}
