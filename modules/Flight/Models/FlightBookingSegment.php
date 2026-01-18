<?php

namespace Modules\Flight\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Flight\Database\Factories\FlightBookingSegmentFactory;
use Modules\Support\Models\BaseModel;

class FlightBookingSegment extends BaseModel
{
    use HasFactory, HasUuid;

    protected $table = 'flight_booking_segments';

    protected function casts(): array
    {
        return [
            'segment_order' => 'integer',
            'is_codeshare' => 'boolean',
            'departure_datetime' => 'datetime',
            'arrival_datetime' => 'datetime',
            'duration_minutes' => 'integer',
            'day_offset' => 'integer',
            'stops_count' => 'integer',
            'stopover_details' => 'array',
            'checked_bags' => 'integer',
            'checked_bag_weight_kg' => 'integer',
            'segment_fare' => 'decimal:2',
            'segment_tax' => 'decimal:2',
            'segment_total' => 'decimal:2',
            'meal_service' => 'array',
            'has_wifi' => 'boolean',
            'has_entertainment' => 'boolean',
        ];
    }

    protected static function newFactory(): FlightBookingSegmentFactory
    {
        return FlightBookingSegmentFactory::new();
    }

    // Relationships
    public function booking(): BelongsTo
    {
        return $this->belongsTo(FlightBooking::class, 'booking_id');
    }

    public function flight(): BelongsTo
    {
        return $this->belongsTo(Flight::class, 'flight_id');
    }

    public function fare(): BelongsTo
    {
        return $this->belongsTo(FlightFare::class, 'fare_id');
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

    public function seats(): HasMany
    {
        return $this->hasMany(FlightBookingSeat::class, 'segment_id');
    }

    public function ancillaries(): HasMany
    {
        return $this->hasMany(FlightBookingAncillary::class, 'segment_id');
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
        $airline = $this->airline?->iata_code ?? '';

        return "{$airline}{$this->flight_number}";
    }

    public function getDurationDisplayAttribute(): string
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        return "{$hours}h {$minutes}m";
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

    public function getCabinDisplayAttribute(): string
    {
        return match ($this->cabin_class) {
            'first' => 'First',
            'business' => 'Business',
            'premium_economy' => 'Premium Econ',
            'economy' => 'Economy',
            default => ucfirst($this->cabin_class),
        };
    }

    public function getBaggageDisplayAttribute(): string
    {
        if ($this->baggage_allowance) {
            return $this->baggage_allowance;
        }
        if ($this->checked_bags > 0) {
            return "{$this->checked_bags}PC × {$this->checked_bag_weight_kg}kg";
        }

        return 'No checked baggage';
    }

    // Scopes
    public function scopeOutbound($query)
    {
        return $query->where('segment_type', 'outbound');
    }

    public function scopeInbound($query)
    {
        return $query->where('segment_type', 'inbound');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeByOrder($query)
    {
        return $query->orderBy('segment_order');
    }

    // Methods
    public function isOutbound(): bool
    {
        return $this->segment_type === 'outbound';
    }

    public function isInbound(): bool
    {
        return $this->segment_type === 'inbound';
    }

    public function isDirect(): bool
    {
        return $this->stops_count === 0;
    }

    public function isOperatedByDifferentAirline(): bool
    {
        return $this->operating_airline_id !== null
            && $this->operating_airline_id !== $this->airline_id;
    }
}
