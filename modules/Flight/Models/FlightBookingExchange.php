<?php

namespace Modules\Flight\Models;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Flight\Database\Factories\FlightBookingExchangeFactory;
use Modules\Support\Models\BaseModel;

class FlightBookingExchange extends BaseModel
{
    use HasFactory, HasUuid;

    protected $table = 'flight_booking_exchanges';

    protected function casts(): array
    {
        return [
            'original_segments' => 'array',
            'new_segments' => 'array',
            'original_departure_date' => 'date',
            'new_departure_date' => 'date',
            'original_fare' => 'decimal:2',
            'new_fare' => 'decimal:2',
            'fare_difference' => 'decimal:2',
            'exchange_fee' => 'decimal:2',
            'service_fee' => 'decimal:2',
            'total_due' => 'decimal:2',
            'total_refund' => 'decimal:2',
            'amount_collected' => 'decimal:2',
            'new_ticket_numbers' => 'array',
            'supplier_response' => 'array',
            'requested_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    protected static function newFactory(): FlightBookingExchangeFactory
    {
        return FlightBookingExchangeFactory::new();
    }

    // Relationships
    public function booking(): BelongsTo
    {
        return $this->belongsTo(FlightBooking::class, 'booking_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getTypeDisplayAttribute(): string
    {
        return match ($this->type) {
            'date_change' => 'Date Change',
            'route_change' => 'Route Change',
            'class_upgrade' => 'Class Upgrade',
            'passenger_name_change' => 'Name Change',
            'full_reissue' => 'Full Reissue',
            default => ucwords(str_replace('_', ' ', $this->type)),
        };
    }

    public function getStatusDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'requested', 'pending_availability', 'pending_payment' => 'warning',
            'processing' => 'info',
            'completed' => 'success',
            'rejected', 'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    public function getFareDifferenceDisplayAttribute(): string
    {
        $prefix = $this->fare_difference >= 0 ? '+' : '';

        return $prefix.number_format($this->fare_difference, 2).' '.$this->currency_code;
    }

    public function getTotalFeesAttribute(): float
    {
        return $this->exchange_fee + $this->service_fee;
    }

    public function getTotalDueDisplayAttribute(): string
    {
        return number_format($this->total_due, 2).' '.$this->currency_code;
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->whereIn('status', ['requested', 'pending_availability', 'pending_payment', 'processing']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForBooking($query, int $bookingId)
    {
        return $query->where('booking_id', $bookingId);
    }

    // Methods
    public function isPending(): bool
    {
        return in_array($this->status, ['requested', 'pending_availability', 'pending_payment', 'processing']);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function requiresPayment(): bool
    {
        return $this->total_due > 0 && $this->amount_collected < $this->total_due;
    }

    public function isUpgrade(): bool
    {
        return $this->fare_difference > 0;
    }

    public function isDowngrade(): bool
    {
        return $this->fare_difference < 0;
    }

    public function getRemainingDue(): float
    {
        return max(0, $this->total_due - $this->amount_collected);
    }
}
