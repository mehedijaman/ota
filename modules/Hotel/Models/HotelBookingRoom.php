<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Support\Models\BaseModel;

class HotelBookingRoom extends BaseModel
{
    use HasFactory;

    protected $table = 'hotel_booking_rooms';

    protected function casts(): array
    {
        return [
            'check_in_date' => 'date',
            'check_out_date' => 'date',
            'adults' => 'integer',
            'children' => 'integer',
            'infants' => 'integer',
            'daily_rates' => 'array',
            'room_rate' => 'decimal:2',
            'extra_adult_charges' => 'decimal:2',
            'extra_child_charges' => 'decimal:2',
            'meal_charges' => 'decimal:2',
            'extra_charges' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'cost_amount' => 'decimal:2',
            'smoking_room' => 'boolean',
            'accessible_room' => 'boolean',
            'connecting_room' => 'boolean',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(HotelBooking::class, 'booking_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(HotelRoom::class, 'room_id');
    }

    public function ratePlan(): BelongsTo
    {
        return $this->belongsTo(HotelRatePlan::class, 'rate_plan_id');
    }

    public function guests(): HasMany
    {
        return $this->hasMany(HotelBookingGuest::class, 'booking_room_id');
    }

    public function primaryGuest()
    {
        return $this->guests()->where('is_primary', true)->first();
    }

    public function leadGuest()
    {
        return $this->guests()->where('is_lead_guest', true)->first();
    }

    // Accessors
    public function getTotalGuestsAttribute(): int
    {
        return $this->adults + $this->children + $this->infants;
    }

    public function getNightsAttribute(): int
    {
        return $this->check_in_date->diffInDays($this->check_out_date);
    }

    public function getMealPlanLabelAttribute(): string
    {
        return match ($this->meal_plan) {
            'room_only' => 'Room Only',
            'breakfast' => 'Breakfast Included',
            'half_board' => 'Half Board',
            'full_board' => 'Full Board',
            'all_inclusive' => 'All Inclusive',
            default => ucfirst(str_replace('_', ' ', $this->meal_plan ?? 'Unknown')),
        };
    }

    // Scopes
    public function scopeForBooking($query, int $bookingId)
    {
        return $query->where('booking_id', $bookingId);
    }

    public function scopeConfirmed($query)
    {
        return $query->whereIn('status', ['confirmed', 'assigned', 'checked_in']);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
}
