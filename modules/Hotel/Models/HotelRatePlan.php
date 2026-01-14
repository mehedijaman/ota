<?php

namespace Modules\Hotel\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Hotel\Database\Factories\HotelRatePlanFactory;
use Modules\Support\Models\BaseModel;

class HotelRatePlan extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'hotel_rate_plans';

    protected function casts(): array
    {
        return [
            'is_refundable' => 'boolean',
            'is_pay_at_hotel' => 'boolean',
            'requires_prepayment' => 'boolean',
            'prepayment_percentage' => 'decimal:2',
            'advance_booking_min' => 'integer',
            'advance_booking_max' => 'integer',
            'min_nights' => 'integer',
            'max_nights' => 'integer',
            'min_rooms' => 'integer',
            'max_rooms' => 'integer',
            'valid_from' => 'date',
            'valid_to' => 'date',
            'applicable_days' => 'array',
            'markup_percentage' => 'decimal:2',
            'discount_percentage' => 'decimal:2',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function newFactory(): HotelRatePlanFactory
    {
        return HotelRatePlanFactory::new();
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    public function cancellationPolicy(): BelongsTo
    {
        return $this->belongsTo(HotelCancellationPolicy::class, 'cancellation_policy_id');
    }

    public function rates(): HasMany
    {
        return $this->hasMany(HotelRate::class, 'rate_plan_id');
    }

    public function bookingRooms(): HasMany
    {
        return $this->hasMany(HotelBookingRoom::class, 'rate_plan_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getMealPlanLabelAttribute(): string
    {
        return match ($this->meal_plan) {
            'room_only' => 'Room Only',
            'breakfast' => 'Breakfast Included',
            'half_board' => 'Half Board',
            'full_board' => 'Full Board',
            'all_inclusive' => 'All Inclusive',
            'breakfast_lunch' => 'Breakfast & Lunch',
            'breakfast_dinner' => 'Breakfast & Dinner',
            'self_catering' => 'Self Catering',
            default => ucfirst(str_replace('_', ' ', $this->meal_plan ?? 'Unknown')),
        };
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeRefundable($query)
    {
        return $query->where('is_refundable', true);
    }

    public function scopeForHotel($query, int $hotelId)
    {
        return $query->where('hotel_id', $hotelId);
    }

    public function scopeValidOn($query, string $date)
    {
        return $query->where(function ($q) use ($date) {
            $q->whereNull('valid_from')->orWhere('valid_from', '<=', $date);
        })->where(function ($q) use ($date) {
            $q->whereNull('valid_to')->orWhere('valid_to', '>=', $date);
        });
    }

    // Methods
    public function isValidForDate(string $date): bool
    {
        if ($this->valid_from && $date < $this->valid_from) {
            return false;
        }
        if ($this->valid_to && $date > $this->valid_to) {
            return false;
        }

        return true;
    }

    public function isApplicableForDayOfWeek(int $dayOfWeek): bool
    {
        if (! $this->applicable_days) {
            return true; // All days if not specified
        }

        return in_array($dayOfWeek, $this->applicable_days);
    }
}
