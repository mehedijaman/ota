<?php

namespace Modules\Hotel\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Support\Models\BaseModel;

class HotelBookingCancellation extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'hotel_booking_cancellations';

    protected function casts(): array
    {
        return [
            'hours_before_checkin' => 'integer',
            'refund_percentage' => 'decimal:2',
            'original_amount' => 'decimal:2',
            'cancellation_fee' => 'decimal:2',
            'refund_amount' => 'decimal:2',
            'refunded_amount' => 'decimal:2',
            'supplier_response' => 'array',
            'supplier_confirmed' => 'boolean',
            'requested_at' => 'datetime',
            'processed_at' => 'datetime',
            'refunded_at' => 'datetime',
            'supplier_notified_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(HotelBooking::class, 'booking_id');
    }

    public function policy(): BelongsTo
    {
        return $this->belongsTo(HotelCancellationPolicy::class, 'policy_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Accessors
    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsApprovedAttribute(): bool
    {
        return in_array($this->status, ['approved', 'refund_pending', 'refunded', 'partially_refunded']);
    }

    public function getIsRejectedAttribute(): bool
    {
        return $this->status === 'rejected';
    }

    public function getIsRefundedAttribute(): bool
    {
        return in_array($this->status, ['refunded', 'partially_refunded']);
    }

    public function getPendingRefundAttribute(): float
    {
        return max(0, $this->refund_amount - $this->refunded_amount);
    }

    public function getReasonCategoryLabelAttribute(): string
    {
        return match ($this->reason_category) {
            'change_of_plans' => 'Change of Plans',
            'found_better_deal' => 'Found Better Deal',
            'personal_emergency' => 'Personal Emergency',
            'medical' => 'Medical Reason',
            'travel_restrictions' => 'Travel Restrictions',
            'natural_disaster' => 'Natural Disaster',
            'hotel_issue' => 'Hotel Issue',
            'wrong_booking' => 'Wrong Booking',
            'duplicate_booking' => 'Duplicate Booking',
            'other' => 'Other',
            default => ucfirst(str_replace('_', ' ', $this->reason_category ?? 'Unknown')),
        };
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->whereIn('status', ['approved', 'refund_pending', 'refunded', 'partially_refunded']);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeAwaitingRefund($query)
    {
        return $query->whereIn('status', ['approved', 'refund_pending']);
    }

    // Methods
    public static function generateCancellationNumber(): string
    {
        do {
            $number = 'CN'.date('Ymd').strtoupper(substr(uniqid(), -6));
        } while (self::where('cancellation_number', $number)->exists());

        return $number;
    }

    public function approve(?int $userId = null): void
    {
        $this->update([
            'status' => 'approved',
            'processed_by' => $userId ?? auth()->id(),
            'processed_at' => now(),
        ]);

        $booking = $this->booking;
        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_by' => $userId ?? auth()->id(),
            'cancellation_reason' => $this->reason,
        ]);

        $booking->recordStatusChange($booking->status, 'cancelled', 'Cancellation approved', $userId);
    }

    public function reject(string $reason, ?int $userId = null): void
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'processed_by' => $userId ?? auth()->id(),
            'processed_at' => now(),
        ]);
    }

    public function recordRefund(float $amount): void
    {
        $newRefundedAmount = $this->refunded_amount + $amount;

        $this->update([
            'refunded_amount' => $newRefundedAmount,
            'refunded_at' => now(),
            'status' => $newRefundedAmount >= $this->refund_amount ? 'refunded' : 'partially_refunded',
        ]);
    }
}
