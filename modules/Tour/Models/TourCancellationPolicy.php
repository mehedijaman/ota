<?php

namespace Modules\Tour\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Support\Models\BaseModel;

class TourCancellationPolicy extends BaseModel
{
    protected $table = 'tour_cancellation_policies';

    protected function casts(): array
    {
        return [
            'refund_percentage' => 'decimal:2',
            'cancellation_fee' => 'decimal:2',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
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
     * Get cancellations using this policy.
     */
    public function cancellations(): HasMany
    {
        return $this->hasMany(TourBookingCancellation::class, 'policy_id');
    }

    /**
     * Scope active policies.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope default policies.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Get applicable policy for days before departure.
     */
    public static function getApplicable(?int $tourId, int $daysBeforeDeparture)
    {
        return self::query()
            ->where(function ($q) use ($tourId) {
                $q->where('tour_id', $tourId)
                    ->orWhereNull('tour_id');
            })
            ->where('is_active', true)
            ->where('days_before_departure', '<=', $daysBeforeDeparture)
            ->orderByDesc('days_before_departure')
            ->first();
    }
}
