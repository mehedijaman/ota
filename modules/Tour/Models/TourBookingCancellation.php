<?php

namespace Modules\Tour\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;

class TourBookingCancellation extends BaseModel
{
    use ActivityLog, SoftDeletes;

    protected $table = 'tour_booking_cancellations';

    protected function casts(): array
    {
        return [
            'refund_percentage' => 'decimal:2',
            'original_amount' => 'decimal:2',
            'cancellation_fee' => 'decimal:2',
            'refund_amount' => 'decimal:2',
            'refunded_amount' => 'decimal:2',
            'requested_at' => 'datetime',
            'processed_at' => 'datetime',
            'refunded_at' => 'datetime',
        ];
    }

    /**
     * Get the booking.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(TourBooking::class, 'booking_id');
    }

    /**
     * Get the policy.
     */
    public function policy(): BelongsTo
    {
        return $this->belongsTo(TourCancellationPolicy::class, 'policy_id');
    }

    /**
     * Get requester.
     */
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Get processor.
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Get pending refund amount.
     */
    public function getPendingRefundAttribute(): float
    {
        return max(0, $this->refund_amount - $this->refunded_amount);
    }

    /**
     * Scope pending cancellations.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope approved cancellations.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Generate unique cancellation number.
     */
    public static function generateCancellationNumber(): string
    {
        do {
            $number = 'CAN'.date('Ymd').strtoupper(substr(uniqid(), -6));
        } while (self::where('cancellation_number', $number)->exists());

        return $number;
    }
}
