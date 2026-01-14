<?php

namespace Modules\Hotel\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Support\Models\BaseModel;

class HotelRateBlackout extends BaseModel
{
    use HasFactory;

    protected $table = 'hotel_rate_blackouts';

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(HotelRoom::class, 'room_id');
    }

    public function ratePlan(): BelongsTo
    {
        return $this->belongsTo(HotelRatePlan::class, 'rate_plan_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForHotel($query, int $hotelId)
    {
        return $query->where('hotel_id', $hotelId);
    }

    public function scopeAffectingDate($query, string $date)
    {
        return $query->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date);
    }

    public function scopeAffectingDateRange($query, string $startDate, string $endDate)
    {
        return $query->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate);
    }

    public function affectsRoom(?int $roomId): bool
    {
        return $this->room_id === null || $this->room_id === $roomId;
    }

    public function affectsRatePlan(?int $ratePlanId): bool
    {
        return $this->rate_plan_id === null || $this->rate_plan_id === $ratePlanId;
    }

    public function isBlocked(string $date, ?int $roomId = null, ?int $ratePlanId = null): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($date < $this->start_date || $date > $this->end_date) {
            return false;
        }

        return $this->affectsRoom($roomId) && $this->affectsRatePlan($ratePlanId);
    }
}
