<?php

namespace Modules\Hotel\Models;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Hotel\Database\Factories\HotelBookingFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\Searchable;

class HotelBooking extends BaseModel
{
    use HasFactory, HasUuid, Searchable, SoftDeletes;

    protected $table = 'hotel_bookings';

    protected function casts(): array
    {
        return [
            'check_in_date' => 'date',
            'check_out_date' => 'date',
            'nights' => 'integer',
            'total_rooms' => 'integer',
            'total_adults' => 'integer',
            'total_children' => 'integer',
            'total_infants' => 'integer',
            'estimated_arrival_time' => 'datetime:H:i',
            'late_checkout_requested' => 'boolean',
            'early_checkin_requested' => 'boolean',
            'subtotal' => 'decimal:2',
            'extras_total' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'service_fee' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'cost_amount' => 'decimal:2',
            'profit_amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'amount_due' => 'decimal:2',
            'payment_due_date' => 'date',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'checked_in_at' => 'datetime',
            'checked_out_at' => 'datetime',
            'no_show_at' => 'datetime',
            'modification_requested_at' => 'datetime',
        ];
    }

    protected static function newFactory(): HotelBookingFactory
    {
        return HotelBookingFactory::new();
    }

    // Relationships
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(HotelSupplier::class, 'supplier_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(\Modules\Customer\Models\Customer::class, 'customer_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(\Modules\Agent\Models\Agent::class, 'agent_id');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(HotelBookingRoom::class, 'booking_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(HotelBookingPayment::class, 'booking_id');
    }

    public function completedPayments(): HasMany
    {
        return $this->payments()->where('status', 'completed');
    }

    public function cancellation(): HasOne
    {
        return $this->hasOne(HotelBookingCancellation::class, 'booking_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(HotelBookingStatusHistory::class, 'booking_id')->orderByDesc('changed_at');
    }

    public function review(): HasOne
    {
        return $this->hasOne(HotelReview::class, 'booking_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getTotalGuestsAttribute(): int
    {
        return $this->total_adults + $this->total_children + $this->total_infants;
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function getIsConfirmedAttribute(): bool
    {
        return in_array($this->status, ['confirmed', 'guaranteed', 'checked_in', 'checked_out', 'completed']);
    }

    public function getIsCancelledAttribute(): bool
    {
        return $this->status === 'cancelled';
    }

    public function getCanBeCancelledAttribute(): bool
    {
        return in_array($this->status, ['pending', 'confirmed', 'guaranteed']);
    }

    public function getStayDurationAttribute(): string
    {
        return $this->nights.' night'.($this->nights > 1 ? 's' : '');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeCheckedIn($query)
    {
        return $query->where('status', 'checked_in');
    }

    public function scopeUpcoming($query)
    {
        return $query->whereIn('status', ['confirmed', 'guaranteed'])
            ->where('check_in_date', '>=', now()->toDateString());
    }

    public function scopeArrivingToday($query)
    {
        return $query->whereIn('status', ['confirmed', 'guaranteed'])
            ->where('check_in_date', now()->toDateString());
    }

    public function scopeDepartingToday($query)
    {
        return $query->where('status', 'checked_in')
            ->where('check_out_date', now()->toDateString());
    }

    public function scopeForHotel($query, int $hotelId)
    {
        return $query->where('hotel_id', $hotelId);
    }

    public function scopeForDateRange($query, string $startDate, string $endDate)
    {
        return $query->where('check_in_date', '<=', $endDate)
            ->where('check_out_date', '>=', $startDate);
    }

    // Methods
    public static function generateBookingNumber(): string
    {
        do {
            $number = 'HB'.date('Ymd').strtoupper(substr(uniqid(), -6));
        } while (self::where('booking_number', $number)->exists());

        return $number;
    }

    public function calculateNights(): int
    {
        return $this->check_in_date->diffInDays($this->check_out_date);
    }

    public function updatePaymentStatus(): void
    {
        $totalPaid = $this->completedPayments()->sum('amount');
        $this->amount_paid = $totalPaid;
        $this->amount_due = max(0, $this->total_amount - $totalPaid);

        if ($totalPaid >= $this->total_amount) {
            $this->payment_status = 'paid';
        } elseif ($totalPaid > 0) {
            $this->payment_status = 'partial';
        } else {
            $this->payment_status = 'unpaid';
        }

        $this->save();
    }

    public function recordStatusChange(?string $fromStatus, string $toStatus, ?string $notes = null, ?int $userId = null): void
    {
        $this->statusHistories()->create([
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'notes' => $notes,
            'changed_by' => $userId ?? auth()->id(),
            'changed_at' => now(),
        ]);
    }
}
