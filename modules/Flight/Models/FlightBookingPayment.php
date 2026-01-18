<?php

namespace Modules\Flight\Models;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Flight\Database\Factories\FlightBookingPaymentFactory;
use Modules\Support\Models\BaseModel;

class FlightBookingPayment extends BaseModel
{
    use HasFactory, HasUuid;

    protected $table = 'flight_booking_payments';

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'exchange_rate' => 'decimal:4',
            'amount_in_base_currency' => 'decimal:2',
            'processing_fee' => 'decimal:2',
            'gateway_fee' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'gateway_response' => 'array',
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    protected static function newFactory(): FlightBookingPaymentFactory
    {
        return FlightBookingPaymentFactory::new();
    }

    // Relationships
    public function booking(): BelongsTo
    {
        return $this->belongsTo(FlightBooking::class, 'booking_id');
    }

    public function originalPayment(): BelongsTo
    {
        return $this->belongsTo(self::class, 'original_payment_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function collectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getAmountDisplayAttribute(): string
    {
        return number_format($this->amount, 2).' '.$this->currency_code;
    }

    public function getMethodDisplayAttribute(): string
    {
        return match ($this->method) {
            'cash' => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            'credit_card' => 'Credit Card',
            'debit_card' => 'Debit Card',
            'bkash' => 'bKash',
            'nagad' => 'Nagad',
            'rocket' => 'Rocket',
            'upay' => 'Upay',
            'paypal' => 'PayPal',
            'stripe' => 'Stripe',
            'points' => 'Points',
            'credit_balance' => 'Credit Balance',
            'cheque' => 'Cheque',
            'corporate_credit' => 'Corporate Credit',
            'agent_credit' => 'Agent Credit',
            default => ucwords(str_replace('_', ' ', $this->method)),
        };
    }

    public function getStatusDisplayAttribute(): string
    {
        return ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending', 'processing' => 'warning',
            'completed' => 'success',
            'failed', 'cancelled' => 'danger',
            'refunded' => 'info',
            default => 'secondary',
        };
    }

    public function getTypeDisplayAttribute(): string
    {
        return ucfirst($this->type);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopePayments($query)
    {
        return $query->where('type', 'payment');
    }

    public function scopeRefunds($query)
    {
        return $query->where('type', 'refund');
    }

    public function scopeForBooking($query, int $bookingId)
    {
        return $query->where('booking_id', $bookingId);
    }

    public function scopeByMethod($query, string $method)
    {
        return $query->where('method', $method);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    // Methods
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isRefund(): bool
    {
        return $this->type === 'refund';
    }

    public function isVerified(): bool
    {
        return (bool) $this->is_verified;
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
        ]);

        $this->booking->recalculateTotals();
    }

    public function markAsFailed(?string $reason = null): void
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
        ]);
    }

    public function verify(?int $userId = null, ?string $notes = null): void
    {
        $this->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by' => $userId ?? auth()->id(),
            'verification_notes' => $notes,
        ]);
    }
}
