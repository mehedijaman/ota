<?php

namespace Modules\Flight\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Flight\Database\Factories\FlightBookingAncillaryFactory;
use Modules\Support\Models\BaseModel;

class FlightBookingAncillary extends BaseModel
{
    use HasFactory;

    protected $table = 'flight_booking_ancillaries';

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'weight_kg' => 'integer',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'supplier_data' => 'array',
        ];
    }

    protected static function newFactory(): FlightBookingAncillaryFactory
    {
        return FlightBookingAncillaryFactory::new();
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
    public function getTypeDisplayAttribute(): string
    {
        return match ($this->type) {
            'baggage' => 'Extra Baggage',
            'meal' => 'Meal',
            'seat' => 'Seat Selection',
            'lounge' => 'Lounge Access',
            'priority_boarding' => 'Priority Boarding',
            'fast_track' => 'Fast Track',
            'insurance' => 'Travel Insurance',
            'transfer' => 'Airport Transfer',
            'car_rental' => 'Car Rental',
            'hotel' => 'Hotel',
            'wifi' => 'In-flight WiFi',
            'entertainment' => 'Entertainment',
            'pet' => 'Pet in Cabin',
            'sports_equipment' => 'Sports Equipment',
            'musical_instrument' => 'Musical Instrument',
            'unaccompanied_minor' => 'Unaccompanied Minor',
            default => ucwords(str_replace('_', ' ', $this->type)),
        };
    }

    public function getPriceDisplayAttribute(): string
    {
        return number_format($this->total_price, 2).' '.$this->currency_code;
    }

    public function getQuantityDisplayAttribute(): string
    {
        if ($this->type === 'baggage' && $this->weight_kg) {
            return "{$this->quantity} × {$this->weight_kg}kg";
        }

        return (string) $this->quantity;
    }

    // Scopes
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeBaggage($query)
    {
        return $query->where('type', 'baggage');
    }

    public function scopeMeals($query)
    {
        return $query->where('type', 'meal');
    }

    public function scopeForBooking($query, int $bookingId)
    {
        return $query->where('booking_id', $bookingId);
    }

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

    // Methods
    public function isBaggage(): bool
    {
        return $this->type === 'baggage';
    }

    public function isMeal(): bool
    {
        return $this->type === 'meal';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }
}
