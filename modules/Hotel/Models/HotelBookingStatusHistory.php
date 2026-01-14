<?php

namespace Modules\Hotel\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Support\Models\BaseModel;

class HotelBookingStatusHistory extends BaseModel
{
    use HasFactory;

    protected $table = 'hotel_booking_status_histories';

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'changed_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(HotelBooking::class, 'booking_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // Accessors
    public function getFromStatusLabelAttribute(): ?string
    {
        return $this->getStatusLabel($this->from_status);
    }

    public function getToStatusLabelAttribute(): string
    {
        return $this->getStatusLabel($this->to_status);
    }

    protected function getStatusLabel(?string $status): string
    {
        if (! $status) {
            return 'New';
        }

        return match ($status) {
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'guaranteed' => 'Guaranteed',
            'modification_pending' => 'Modification Pending',
            'checked_in' => 'Checked In',
            'checked_out' => 'Checked Out',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'no_show' => 'No Show',
            'refunded' => 'Refunded',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    // Scopes
    public function scopeForBooking($query, int $bookingId)
    {
        return $query->where('booking_id', $bookingId);
    }

    public function scopeRecent($query, int $limit = 10)
    {
        return $query->orderByDesc('changed_at')->limit($limit);
    }

    public function scopeToStatus($query, string $status)
    {
        return $query->where('to_status', $status);
    }
}
