<?php

namespace Modules\Flight\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Agent\Models\Agent;
use Modules\Customer\Models\Customer;
use Modules\Flight\Database\Factories\FlightSearchLogFactory;
use Modules\Support\Models\BaseModel;

class FlightSearchLog extends BaseModel
{
    use HasFactory;

    protected $table = 'flight_search_logs';

    protected function casts(): array
    {
        return [
            'departure_date' => 'date',
            'return_date' => 'date',
            'adults' => 'integer',
            'children' => 'integer',
            'infants' => 'integer',
            'direct_only' => 'boolean',
            'flexible_dates' => 'boolean',
            'results_count' => 'integer',
            'lowest_price' => 'decimal:2',
            'highest_price' => 'decimal:2',
            'response_time_ms' => 'integer',
            'suppliers_queried' => 'array',
            'supplier_response_times' => 'array',
            'converted_to_booking' => 'boolean',
        ];
    }

    protected static function newFactory(): FlightSearchLogFactory
    {
        return FlightSearchLogFactory::new();
    }

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function originAirport(): BelongsTo
    {
        return $this->belongsTo(FlightAirport::class, 'origin_airport_id');
    }

    public function destinationAirport(): BelongsTo
    {
        return $this->belongsTo(FlightAirport::class, 'destination_airport_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(FlightBooking::class, 'booking_id');
    }

    // Accessors
    public function getRouteAttribute(): string
    {
        $origin = $this->origin_code ?? $this->originAirport?->iata_code ?? '???';
        $destination = $this->destination_code ?? $this->destinationAirport?->iata_code ?? '???';

        return "{$origin} → {$destination}";
    }

    public function getTripTypeDisplayAttribute(): string
    {
        return match ($this->trip_type) {
            'one_way' => 'One Way',
            'round_trip' => 'Round Trip',
            'multi_city' => 'Multi-City',
            default => ucfirst($this->trip_type),
        };
    }

    public function getPassengerCountAttribute(): int
    {
        return $this->adults + $this->children + $this->infants;
    }

    public function getPassengerSummaryAttribute(): string
    {
        $parts = [];
        if ($this->adults > 0) {
            $parts[] = "{$this->adults}A";
        }
        if ($this->children > 0) {
            $parts[] = "{$this->children}C";
        }
        if ($this->infants > 0) {
            $parts[] = "{$this->infants}I";
        }

        return implode(', ', $parts);
    }

    public function getResponseTimeDisplayAttribute(): string
    {
        if (! $this->response_time_ms) {
            return 'N/A';
        }

        if ($this->response_time_ms < 1000) {
            return "{$this->response_time_ms}ms";
        }

        return round($this->response_time_ms / 1000, 2).'s';
    }

    // Scopes
    public function scopeConverted($query)
    {
        return $query->where('converted_to_booking', true);
    }

    public function scopeNotConverted($query)
    {
        return $query->where('converted_to_booking', false);
    }

    public function scopeForRoute($query, string $origin, string $destination)
    {
        return $query->where('origin_code', $origin)
            ->where('destination_code', $destination);
    }

    public function scopeForCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeFromSource($query, string $source)
    {
        return $query->where('source', $source);
    }

    public function scopeDepartingOn($query, $date)
    {
        return $query->whereDate('departure_date', $date);
    }

    public function scopeRecent($query)
    {
        return $query->orderByDesc('created_at');
    }

    public function scopeWithResults($query)
    {
        return $query->where('results_count', '>', 0);
    }

    public function scopeNoResults($query)
    {
        return $query->where('results_count', 0);
    }

    // Methods
    public function markAsConverted(int $bookingId): void
    {
        $this->update([
            'converted_to_booking' => true,
            'booking_id' => $bookingId,
        ]);
    }
}
