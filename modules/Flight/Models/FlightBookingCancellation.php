<?php

namespace Modules\Flight\Models;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Flight\Database\Factories\FlightBookingCancellationFactory;
use Modules\Support\Models\BaseModel;

class FlightBookingCancellation extends BaseModel
{
    use HasFactory, HasUuid;

    protected $table = 'flight_booking_cancellations';

    protected function casts(): array
    {
        return [
            'cancelled_segments' => 'array',
            'cancelled_passengers' => 'array',
            'booking_amount' => 'decimal:2',
            'airline_penalty' => 'decimal:2',
            'service_charge' => 'decimal:2',
            'total_deduction' => 'decimal:2',
            'refundable_amount' => 'decimal:2',
            'refunded_amount' => 'decimal:2',
            'refund_approved_at' => 'datetime',
            'refund_processed_at' => 'datetime',
            'refund_details' => 'array',
            'supplier_response' => 'array',
            'supporting_documents' => 'array',
            'requested_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    protected static function newFactory(): FlightBookingCancellationFactory
    {
        return FlightBookingCancellationFactory::new();
    }

    // Relationships
    public function booking(): BelongsTo
    {
        return $this->belongsTo(FlightBooking::class, 'booking_id');
    }

    public function refundApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'refund_approved_by');
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
            'full' => 'Full Cancellation',
            'partial' => 'Partial Cancellation',
            'airline_initiated' => 'Airline Initiated',
            'schedule_change' => 'Schedule Change',
            'void' => 'Void',
            default => ucfirst($this->type),
        };
    }

    public function getReasonDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->reason_category));
    }

    public function getStatusDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    public function getRefundStatusDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->refund_status));
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'requested', 'pending_airline' => 'warning',
            'confirmed', 'completed' => 'success',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }

    public function getRefundableDisplayAttribute(): string
    {
        return number_format($this->refundable_amount, 2).' '.$this->currency_code;
    }

    public function getPenaltyDisplayAttribute(): string
    {
        $total = $this->airline_penalty + $this->service_charge;

        return number_format($total, 2).' '.$this->currency_code;
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->whereIn('status', ['requested', 'pending_airline']);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeAwaitingRefund($query)
    {
        return $query->whereIn('refund_status', ['pending_approval', 'approved', 'processing']);
    }

    public function scopeRefunded($query)
    {
        return $query->whereIn('refund_status', ['refunded_to_customer', 'refunded_to_wallet', 'refunded_to_card', 'refunded_to_bank']);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // Methods
    public function isPending(): bool
    {
        return in_array($this->status, ['requested', 'pending_airline']);
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isRefundPending(): bool
    {
        return in_array($this->refund_status, ['pending_approval', 'approved', 'processing']);
    }

    public function isRefunded(): bool
    {
        return in_array($this->refund_status, ['refunded_to_customer', 'refunded_to_wallet', 'refunded_to_card', 'refunded_to_bank', 'partially_refunded']);
    }

    public function isFull(): bool
    {
        return $this->type === 'full';
    }

    public function isPartial(): bool
    {
        return $this->type === 'partial';
    }

    public function getRemainingRefund(): float
    {
        return $this->refundable_amount - $this->refunded_amount;
    }

    public function approveRefund(?int $userId = null): void
    {
        $this->update([
            'refund_status' => 'approved',
            'refund_approved_at' => now(),
            'refund_approved_by' => $userId ?? auth()->id(),
        ]);
    }
}
