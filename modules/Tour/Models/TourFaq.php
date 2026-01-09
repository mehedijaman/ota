<?php

namespace Modules\Tour\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Support\Models\BaseModel;

class TourFaq extends BaseModel
{
    protected $table = 'tour_faqs';

    protected function casts(): array
    {
        return [
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
     * Scope active FAQs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
