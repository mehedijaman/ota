<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Support\Models\BaseModel;

class HotelRate extends BaseModel
{
    use HasFactory;

    protected $table = 'hotel_rates';

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'base_rate' => 'decimal:2',
            'extra_adult_rate' => 'decimal:2',
            'extra_child_rate' => 'decimal:2',
            'single_occupancy_rate' => 'decimal:2',
            'extra_bed_rate' => 'decimal:2',
            'cost_rate' => 'decimal:2',
            'selling_rate' => 'decimal:2',
            'rack_rate' => 'decimal:2',
            'min_stay' => 'integer',
            'max_stay' => 'integer',
            'min_advance_days' => 'integer',
            'is_closed' => 'boolean',
            'close_on_arrival' => 'boolean',
            'close_on_departure' => 'boolean',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(HotelRoom::class, 'room_id');
    }

    public function ratePlan(): BelongsTo
    {
        return $this->belongsTo(HotelRatePlan::class, 'rate_plan_id');
    }

    // Accessors
    public function getEffectiveRateAttribute(): float
    {
        return $this->selling_rate ?? $this->base_rate ?? 0;
    }

    public function getProfitMarginAttribute(): float
    {
        if (! $this->cost_rate || $this->cost_rate == 0) {
            return 0;
        }

        return round((($this->effective_rate - $this->cost_rate) / $this->cost_rate) * 100, 2);
    }

    // Scopes
    public function scopeForRoom($query, int $roomId)
    {
        return $query->where('room_id', $roomId);
    }

    public function scopeForRatePlan($query, int $ratePlanId)
    {
        return $query->where('rate_plan_id', $ratePlanId);
    }

    public function scopeForDate($query, string $date)
    {
        return $query->where('date', $date);
    }

    public function scopeForDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_closed', false);
    }

    public function scopeAllowArrival($query)
    {
        return $query->where('close_on_arrival', false);
    }

    public function scopeAllowDeparture($query)
    {
        return $query->where('close_on_departure', false);
    }

    // Methods
    public function isAvailable(): bool
    {
        return ! $this->is_closed;
    }

    public function canCheckIn(): bool
    {
        return ! $this->is_closed && ! $this->close_on_arrival;
    }

    public function canCheckOut(): bool
    {
        return ! $this->close_on_departure;
    }

    public function calculateTotalForOccupancy(int $adults, int $children = 0, int $standardOccupancy = 2): float
    {
        $total = $this->base_rate;

        $extraAdults = max(0, $adults - $standardOccupancy);
        $total += $extraAdults * ($this->extra_adult_rate ?? 0);

        $total += $children * ($this->extra_child_rate ?? 0);

        return round($total, 2);
    }
}
