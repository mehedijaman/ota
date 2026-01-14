<?php

namespace Modules\Hotel\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Support\Models\BaseModel;

class HotelBookingPayment extends BaseModel
{
    use HasFactory;

    protected $table = 'hotel_booking_payments';

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'failed_at' => 'datetime',
            'refunded_at' => 'datetime',
            'refund_amount' => 'decimal:2',
            'bank_deposit_date' => 'date',
            'gateway_response' => 'array',
            'metadata' => 'array',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(HotelBooking::class, 'booking_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function refundedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'refunded_by');
    }

    // Accessors
    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsRefundedAttribute(): bool
    {
        return in_array($this->status, ['refunded', 'partially_refunded']);
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'bkash' => 'bKash',
            'nagad' => 'Nagad',
            'rocket' => 'Rocket',
            'card' => 'Credit/Debit Card',
            'bank' => 'Bank Transfer',
            'cash' => 'Cash',
            'check' => 'Check',
            'online' => 'Online Payment',
            default => ucfirst($this->payment_method ?? 'Unknown'),
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'deposit' => 'Deposit',
            'partial' => 'Partial Payment',
            'full' => 'Full Payment',
            'additional' => 'Additional Charge',
            'damage_deposit' => 'Damage Deposit',
            'refund' => 'Refund',
            default => ucfirst($this->type ?? 'Payment'),
        };
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

    public function scopeRefunded($query)
    {
        return $query->whereIn('status', ['refunded', 'partially_refunded']);
    }

    public function scopeForBooking($query, int $bookingId)
    {
        return $query->where('booking_id', $bookingId);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByMethod($query, string $method)
    {
        return $query->where('payment_method', $method);
    }

    // Methods
    public static function generateTransactionId(): string
    {
        do {
            $id = 'TXN'.date('YmdHis').strtoupper(substr(uniqid(), -4));
        } while (self::where('transaction_id', $id)->exists());

        return $id;
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        $this->booking->updatePaymentStatus();
    }

    public function markAsFailed(string $reason): void
    {
        $this->update([
            'status' => 'failed',
            'failed_at' => now(),
            'failure_reason' => $reason,
        ]);
    }

    public function processRefund(float $amount, string $reason, ?int $userId = null): void
    {
        $this->update([
            'status' => $amount >= $this->amount ? 'refunded' : 'partially_refunded',
            'refunded_at' => now(),
            'refund_amount' => $amount,
            'refund_reason' => $reason,
            'refunded_by' => $userId ?? auth()->id(),
        ]);

        $this->booking->updatePaymentStatus();
    }
}
