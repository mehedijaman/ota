<?php

namespace Modules\Tour\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Support\Models\BaseModel;
use Modules\Tour\Database\Factories\TourPricingFactory;

class TourPricing extends BaseModel
{
    use HasFactory;

    protected $table = 'tour_pricings';

    protected static function newFactory()
    {
        return TourPricingFactory::new();
    }

    protected function casts(): array
    {
        return [
            'adult_price' => 'decimal:2',
            'child_price' => 'decimal:2',
            'infant_price' => 'decimal:2',
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
     * Scope active pricing.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope valid pricing for a date.
     */
    public function scopeValidFor($query, $date = null)
    {
        $date = $date ?? now();

        return $query->where(function ($q) use ($date) {
            $q->whereNull('valid_from')
                ->orWhere('valid_from', '<=', $date);
        })->where(function ($q) use ($date) {
            $q->whereNull('valid_until')
                ->orWhere('valid_until', '>=', $date);
        });
    }
}
