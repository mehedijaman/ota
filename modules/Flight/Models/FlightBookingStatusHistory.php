<?php

namespace Modules\Flight\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Flight\Database\Factories\FlightBookingStatusHistoryFactory;
use Modules\Support\Models\BaseModel;

class FlightBookingStatusHistory extends BaseModel
{
    use HasFactory;

    protected $table = 'flight_booking_status_histories';

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): FlightBookingStatusHistoryFactory
    {
        return FlightBookingStatusHistoryFactory::new();
    }

    // Relationships
    public function booking(): BelongsTo
    {
        return $this->belongsTo(FlightBooking::class, 'booking_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // Accessors
    public function getStatusChangeDisplayAttribute(): string
    {
        if ($this->old_status) {
            return ucwords(str_replace('_', ' ', $this->old_status)).' → '.ucwords(str_replace('_', ' ', $this->new_status));
        }

        return ucwords(str_replace('_', ' ', $this->new_status));
    }

    public function getSourceDisplayAttribute(): string
    {
        return ucfirst($this->source);
    }

    // Scopes
    public function scopeForBooking($query, int $bookingId)
    {
        return $query->where('booking_id', $bookingId);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('status_type', $type);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('changed_by', $userId);
    }

    public function scopeRecent($query, int $limit = 10)
    {
        return $query->orderByDesc('created_at')->limit($limit);
    }
}
