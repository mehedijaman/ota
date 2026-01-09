<?php

namespace Modules\Tour\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Support\Models\BaseModel;

class TourBookingExtra extends BaseModel
{
    protected $table = 'tour_booking_extras';

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
    }

    /**
     * Get the booking.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(TourBooking::class, 'booking_id');
    }

    /**
     * Get the extra.
     */
    public function extra(): BelongsTo
    {
        return $this->belongsTo(TourExtra::class, 'extra_id');
    }
}
