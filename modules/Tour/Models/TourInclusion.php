<?php

namespace Modules\Tour\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Support\Models\BaseModel;

class TourInclusion extends BaseModel
{
    protected $table = 'tour_inclusions';

    /**
     * Get the tour.
     */
    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    /**
     * Scope inclusions only.
     */
    public function scopeInclusions($query)
    {
        return $query->where('type', 'inclusion');
    }

    /**
     * Scope exclusions only.
     */
    public function scopeExclusions($query)
    {
        return $query->where('type', 'exclusion');
    }
}
