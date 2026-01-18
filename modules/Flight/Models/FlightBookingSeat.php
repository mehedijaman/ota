<?php

namespace Modules\Flight\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Flight\Database\Factories\FlightBookingSeatFactory;
use Modules\Support\Models\BaseModel;

class FlightBookingSeat extends BaseModel
{
    use HasFactory;

    protected $table = 'flight_booking_seats';

    protected function casts(): array
    {
        return [
            'is_window' => 'boolean',
            'is_aisle' => 'boolean',
            'is_exit_row' => 'boolean',
            'has_extra_legroom' => 'boolean',
            'is_reclinable' => 'boolean',
            'has_power_outlet' => 'boolean',
            'has_entertainment_screen' => 'boolean',
            'price' => 'decimal:2',
            'is_complimentary' => 'boolean',
        ];
    }

    protected static function newFactory(): FlightBookingSeatFactory
    {
        return FlightBookingSeatFactory::new();
    }

    // Relationships
    public function booking(): BelongsTo
    {
        return $this->belongsTo(FlightBooking::class, 'booking_id');
    }

    public function segment(): BelongsTo
    {
        return $this->belongsTo(FlightBookingSegment::class, 'segment_id');
    }

    public function passenger(): BelongsTo
    {
        return $this->belongsTo(FlightBookingPassenger::class, 'passenger_id');
    }

    // Accessors
    public function getSeatDisplayAttribute(): string
    {
        return "{$this->seat_number} ({$this->seat_type_display})";
    }

    public function getSeatTypeDisplayAttribute(): string
    {
        return match ($this->seat_type) {
            'window' => 'Window',
            'aisle' => 'Aisle',
            'middle' => 'Middle',
            'exit_row' => 'Exit Row',
            'extra_legroom' => 'Extra Legroom',
            'bulkhead' => 'Bulkhead',
            'bassinet' => 'Bassinet',
            default => ucfirst($this->seat_type),
        };
    }

    public function getPriceDisplayAttribute(): string
    {
        if ($this->is_complimentary) {
            return 'Free';
        }

        return number_format($this->price, 2).' '.$this->currency_code;
    }

    // Scopes
    public function scopeForSegment($query, int $segmentId)
    {
        return $query->where('segment_id', $segmentId);
    }

    public function scopeForPassenger($query, int $passengerId)
    {
        return $query->where('passenger_id', $passengerId);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopePaid($query)
    {
        return $query->where('is_complimentary', false)->where('price', '>', 0);
    }

    // Methods
    public function isPremium(): bool
    {
        return $this->is_exit_row
            || $this->has_extra_legroom
            || $this->seat_type === 'extra_legroom';
    }

    public function isWindow(): bool
    {
        return (bool) $this->is_window;
    }

    public function isAisle(): bool
    {
        return (bool) $this->is_aisle;
    }
}
