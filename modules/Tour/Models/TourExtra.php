<?php

namespace Modules\Tour\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Support\Models\BaseModel;

class TourExtra extends BaseModel
{
    protected $table = 'tour_extras';

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_required' => 'boolean',
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
     * Get booking extras using this extra.
     */
    public function bookingExtras(): HasMany
    {
        return $this->hasMany(TourBookingExtra::class, 'extra_id');
    }

    /**
     * Scope active extras.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope global extras (not tour-specific).
     */
    public function scopeGlobal($query)
    {
        return $query->whereNull('tour_id');
    }
}
