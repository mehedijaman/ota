<?php

namespace Modules\Tour\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Support\Models\BaseModel;

class TourDepartureDate extends BaseModel
{
    protected $table = 'tour_departure_dates';

    protected function casts(): array
    {
        return [
            'departure_date' => 'date',
            'return_date' => 'date',
            'price_adjustment' => 'decimal:2',
        ];
    }

    /**
     * Get the tour.
     */
    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    /**
     * Get bookings for this departure date.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(TourBooking::class, 'departure_date_id');
    }

    /**
     * Get remaining slots.
     */
    public function getRemainingAttribute(): int
    {
        return max(0, $this->available_slots - $this->booked_slots);
    }

    /**
     * Scope available dates.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')
            ->where('departure_date', '>=', now()->toDateString());
    }

    /**
     * Scope upcoming dates.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('departure_date', '>=', now()->toDateString())
            ->orderBy('departure_date');
    }
}
