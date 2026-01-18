<?php

namespace Modules\Flight\Models;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Flight\Database\Factories\FlightFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\Searchable;

class Flight extends BaseModel
{
    use HasFactory, HasUuid, Searchable, SoftDeletes;

    protected $table = 'flights';

    protected function casts(): array
    {
        return [
            'scheduled_departure_time' => 'datetime:H:i',
            'scheduled_arrival_time' => 'datetime:H:i',
            'duration_minutes' => 'integer',
            'crosses_midnight' => 'boolean',
            'day_offset' => 'integer',
            'operates_monday' => 'boolean',
            'operates_tuesday' => 'boolean',
            'operates_wednesday' => 'boolean',
            'operates_thursday' => 'boolean',
            'operates_friday' => 'boolean',
            'operates_saturday' => 'boolean',
            'operates_sunday' => 'boolean',
            'effective_from' => 'date',
            'effective_until' => 'date',
            'stops_count' => 'integer',
            'via_airports' => 'array',
            'is_codeshare' => 'boolean',
            'is_red_eye' => 'boolean',
            'cabin_services' => 'array',
            'has_wifi' => 'boolean',
            'has_power_outlet' => 'boolean',
            'has_entertainment' => 'boolean',
            'is_active' => 'boolean',
            'base_price' => 'decimal:2',
        ];
    }

    protected static function newFactory(): FlightFactory
    {
        return FlightFactory::new();
    }

    // Relationships
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(FlightSupplier::class, 'supplier_id');
    }

    public function airline(): BelongsTo
    {
        return $this->belongsTo(FlightAirline::class, 'airline_id');
    }

    public function operatingAirline(): BelongsTo
    {
        return $this->belongsTo(FlightAirline::class, 'operating_airline_id');
    }

    public function departureAirport(): BelongsTo
    {
        return $this->belongsTo(FlightAirport::class, 'departure_airport_id');
    }

    public function arrivalAirport(): BelongsTo
    {
        return $this->belongsTo(FlightAirport::class, 'arrival_airport_id');
    }

    public function aircraftType(): BelongsTo
    {
        return $this->belongsTo(FlightAircraftType::class, 'aircraft_type_id');
    }

    public function fares(): HasMany
    {
        return $this->hasMany(FlightFare::class, 'flight_id');
    }

    public function activeFares(): HasMany
    {
        return $this->fares()->where('is_active', true)->where('is_soldout', false);
    }

    public function bookingSegments(): HasMany
    {
        return $this->hasMany(FlightBookingSegment::class, 'flight_id');
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
    public function getRouteAttribute(): string
    {
        $dep = $this->departureAirport?->iata_code ?? '???';
        $arr = $this->arrivalAirport?->iata_code ?? '???';

        return "{$dep} → {$arr}";
    }

    public function getFlightDisplayAttribute(): string
    {
        return ($this->airline?->iata_code ?? '').$this->flight_number;
    }

    public function getDurationDisplayAttribute(): string
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        return "{$hours}h {$minutes}m";
    }

    public function getOperatingDaysAttribute(): array
    {
        $days = [];
        if ($this->operates_monday) {
            $days[] = 'Mon';
        }
        if ($this->operates_tuesday) {
            $days[] = 'Tue';
        }
        if ($this->operates_wednesday) {
            $days[] = 'Wed';
        }
        if ($this->operates_thursday) {
            $days[] = 'Thu';
        }
        if ($this->operates_friday) {
            $days[] = 'Fri';
        }
        if ($this->operates_saturday) {
            $days[] = 'Sat';
        }
        if ($this->operates_sunday) {
            $days[] = 'Sun';
        }

        return $days;
    }

    public function getOperatingDaysDisplayAttribute(): string
    {
        $days = $this->operating_days;
        if (count($days) === 7) {
            return 'Daily';
        }

        return implode(', ', $days);
    }

    public function getStopsDisplayAttribute(): string
    {
        if ($this->stops_count === 0) {
            return 'Direct';
        }
        if ($this->stops_count === 1) {
            return '1 Stop';
        }

        return "{$this->stops_count} Stops";
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeDirect($query)
    {
        return $query->where('stops_count', 0);
    }

    public function scopeWithStops($query)
    {
        return $query->where('stops_count', '>', 0);
    }

    public function scopeForRoute($query, int $departureId, int $arrivalId)
    {
        return $query->where('departure_airport_id', $departureId)
            ->where('arrival_airport_id', $arrivalId);
    }

    public function scopeForAirline($query, int $airlineId)
    {
        return $query->where('airline_id', $airlineId);
    }

    public function scopeOperatesOn($query, string $day)
    {
        return $query->where("operates_{$day}", true);
    }

    public function scopeEffectiveOn($query, $date)
    {
        return $query->where(function ($q) use ($date) {
            $q->whereNull('effective_from')
                ->orWhere('effective_from', '<=', $date);
        })->where(function ($q) use ($date) {
            $q->whereNull('effective_until')
                ->orWhere('effective_until', '>=', $date);
        });
    }

    public function scopeCodeshare($query)
    {
        return $query->where('is_codeshare', true);
    }

    // Methods
    public function isDirect(): bool
    {
        return $this->stops_count === 0;
    }

    public function isOperatedByDifferentAirline(): bool
    {
        return $this->operating_airline_id !== null
            && $this->operating_airline_id !== $this->airline_id;
    }

    public function operatesOnDate($date): bool
    {
        $dayOfWeek = strtolower($date->format('l'));
        $property = "operates_{$dayOfWeek}";

        if (! $this->$property) {
            return false;
        }

        if ($this->effective_from && $date->lt($this->effective_from)) {
            return false;
        }

        if ($this->effective_until && $date->gt($this->effective_until)) {
            return false;
        }

        return true;
    }

    public function getLowestFare()
    {
        return $this->activeFares()->orderBy('adult_total')->first();
    }
}
