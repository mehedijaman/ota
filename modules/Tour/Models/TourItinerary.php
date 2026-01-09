<?php

namespace Modules\Tour\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Support\Models\BaseModel;

class TourItinerary extends BaseModel
{
    protected $table = 'tour_itineraries';

    protected function casts(): array
    {
        return [
            'meals_included' => 'array',
            'activities' => 'array',
            'highlights' => 'array',
            'distance_km' => 'decimal:2',
        ];
    }

    /**
     * Get the tour.
     */
    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }
}
