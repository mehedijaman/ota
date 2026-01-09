<?php

namespace Modules\Tour\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Support\Models\BaseModel;

class TourHighlight extends BaseModel
{
    protected $table = 'tour_highlights';

    /**
     * Get the tour.
     */
    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }
}
