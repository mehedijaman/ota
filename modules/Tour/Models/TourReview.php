<?php

namespace Modules\Tour\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Support\Models\BaseModel;

class TourReview extends BaseModel
{
    use SoftDeletes;

    protected $table = 'tour_reviews';

    protected function casts(): array
    {
        return [
            'rating' => 'decimal:1',
            'pros' => 'array',
            'cons' => 'array',
            'is_verified' => 'boolean',
            'is_approved' => 'boolean',
            'is_featured' => 'boolean',
            'travel_date' => 'date',
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
     * Get the user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the booking.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(TourBooking::class, 'booking_id');
    }

    /**
     * Scope approved reviews.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope featured reviews.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
