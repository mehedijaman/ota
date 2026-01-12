<?php

namespace Modules\Tour\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Support\Models\BaseModel;
use Modules\Tour\Database\Factories\TourBookingExtraFactory;

class TourBookingExtra extends BaseModel
{
    use HasFactory;

    protected $table = 'tour_booking_extras';

    protected static function newFactory()
    {
        return TourBookingExtraFactory::new();
    }

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
