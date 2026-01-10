<?php

namespace Modules\Tour\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Support\Models\BaseModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Tour\Database\Factories\TourExtraFactory;

class TourExtra extends BaseModel
{
    use HasFactory;

    protected $table = 'tour_extras';

    protected static function newFactory()
    {
        return TourExtraFactory::new();
    }

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
