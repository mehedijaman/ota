<?php

namespace Modules\Flight\Models;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Flight\Database\Factories\FlightFareFactory;
use Modules\Support\Models\BaseModel;

class FlightFare extends BaseModel
{
    use HasFactory, HasUuid;

    protected $table = 'flight_fares';

    protected function casts(): array
    {
        return [
            'valid_from' => 'date',
            'valid_until' => 'date',
            'adult_base_fare' => 'decimal:2',
            'adult_tax' => 'decimal:2',
            'adult_fee' => 'decimal:2',
            'adult_total' => 'decimal:2',
            'child_base_fare' => 'decimal:2',
            'child_tax' => 'decimal:2',
            'child_fee' => 'decimal:2',
            'child_total' => 'decimal:2',
            'child_discount_percent' => 'integer',
            'infant_base_fare' => 'decimal:2',
            'infant_tax' => 'decimal:2',
            'infant_fee' => 'decimal:2',
            'infant_total' => 'decimal:2',
            'infant_discount_percent' => 'integer',
            'exchange_rate' => 'decimal:4',
            'tax_breakdown' => 'array',
            'total_seats' => 'integer',
            'available_seats' => 'integer',
            'sold_seats' => 'integer',
            'blocked_seats' => 'integer',
            'net_cost' => 'decimal:2',
            'markup_amount' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'supplier_fare_data' => 'array',
            'min_booking_days' => 'integer',
            'max_booking_days' => 'integer',
            'is_combinable' => 'boolean',
            'is_active' => 'boolean',
            'is_soldout' => 'boolean',
            'last_synced_at' => 'datetime',
        ];
    }

    protected static function newFactory(): FlightFareFactory
    {
        return FlightFareFactory::new();
    }

    // Relationships
    public function flight(): BelongsTo
    {
        return $this->belongsTo(Flight::class, 'flight_id');
    }

    public function fareClass(): BelongsTo
    {
        return $this->belongsTo(FlightFareClass::class, 'fare_class_id');
    }

    public function bookingSegments(): HasMany
    {
        return $this->hasMany(FlightBookingSegment::class, 'fare_id');
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
    public function getPriceDisplayAttribute(): string
    {
        return number_format($this->adult_total, 2).' '.$this->currency_code;
    }

    public function getAvailabilityDisplayAttribute(): string
    {
        if ($this->is_soldout) {
            return 'Sold Out';
        }
        if ($this->available_seats <= 3) {
            return "{$this->available_seats} seats left";
        }

        return 'Available';
    }

    public function getProfitMarginAttribute(): float
    {
        if ($this->net_cost <= 0) {
            return 0;
        }

        return round(($this->adult_total - $this->net_cost) / $this->net_cost * 100, 2);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_soldout', false)
            ->where('available_seats', '>', 0);
    }

    public function scopeValidOn($query, $date)
    {
        return $query->where('valid_from', '<=', $date)
            ->where('valid_until', '>=', $date);
    }

    public function scopeForFlight($query, int $flightId)
    {
        return $query->where('flight_id', $flightId);
    }

    public function scopeForFareClass($query, int $fareClassId)
    {
        return $query->where('fare_class_id', $fareClassId);
    }

    public function scopeCheapest($query)
    {
        return $query->orderBy('adult_total', 'asc');
    }

    public function scopeByPrice($query, string $direction = 'asc')
    {
        return $query->orderBy('adult_total', $direction);
    }

    // Methods
    public function isAvailable(): bool
    {
        return $this->is_active && ! $this->is_soldout && $this->available_seats > 0;
    }

    public function hasLowAvailability(int $threshold = 5): bool
    {
        return $this->available_seats <= $threshold;
    }

    public function calculateTotalForPassengers(int $adults, int $children = 0, int $infants = 0): float
    {
        $total = $adults * $this->adult_total;

        if ($children > 0 && $this->child_total) {
            $total += $children * $this->child_total;
        }

        if ($infants > 0 && $this->infant_total) {
            $total += $infants * $this->infant_total;
        }

        return round($total, 2);
    }

    public function decrementSeats(int $count = 1): void
    {
        $this->decrement('available_seats', $count);
        $this->increment('sold_seats', $count);

        if ($this->available_seats <= 0) {
            $this->update(['is_soldout' => true]);
        }
    }

    public function incrementSeats(int $count = 1): void
    {
        $this->increment('available_seats', $count);
        $this->decrement('sold_seats', $count);

        if ($this->is_soldout && $this->available_seats > 0) {
            $this->update(['is_soldout' => false]);
        }
    }
}
