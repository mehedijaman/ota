<?php

namespace Modules\Hotel\Models;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Hotel\Database\Factories\HotelReviewFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\Searchable;

class HotelReview extends BaseModel
{
    use HasFactory, HasUuid, Searchable, SoftDeletes;

    protected $table = 'hotel_reviews';

    protected function casts(): array
    {
        return [
            'overall_rating' => 'integer',
            'cleanliness_rating' => 'integer',
            'service_rating' => 'integer',
            'location_rating' => 'integer',
            'value_rating' => 'integer',
            'facilities_rating' => 'integer',
            'comfort_rating' => 'integer',
            'food_rating' => 'integer',
            'stayed_in' => 'date',
            'nights_stayed' => 'integer',
            'is_verified' => 'boolean',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'is_anonymous' => 'boolean',
            'helpful_count' => 'integer',
            'not_helpful_count' => 'integer',
            'reported_count' => 'integer',
            'hotel_responded_at' => 'datetime',
            'moderated_at' => 'datetime',
        ];
    }

    protected static function newFactory(): HotelReviewFactory
    {
        return HotelReviewFactory::new();
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(HotelBooking::class, 'booking_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(\Modules\Customer\Models\Customer::class, 'customer_id');
    }

    public function hotelRespondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hotel_responded_by');
    }

    public function moderatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getAverageSubRatingAttribute(): float
    {
        $ratings = array_filter([
            $this->cleanliness_rating,
            $this->service_rating,
            $this->location_rating,
            $this->value_rating,
            $this->facilities_rating,
            $this->comfort_rating,
            $this->food_rating,
        ]);

        if (empty($ratings)) {
            return $this->overall_rating;
        }

        return round(array_sum($ratings) / count($ratings), 1);
    }

    public function getReviewerDisplayNameAttribute(): string
    {
        if ($this->is_anonymous) {
            return 'Anonymous Guest';
        }

        return $this->reviewer_name ?? $this->customer?->full_name ?? 'Guest';
    }

    public function getTravelTypeLabelAttribute(): ?string
    {
        if (! $this->travel_type) {
            return null;
        }

        return match ($this->travel_type) {
            'business' => 'Business Trip',
            'leisure' => 'Leisure Trip',
            'family' => 'Family Vacation',
            'couple' => 'Romantic Getaway',
            'solo' => 'Solo Travel',
            'group' => 'Group Trip',
            'friends' => 'Trip with Friends',
            default => ucfirst($this->travel_type),
        };
    }

    public function getRatingLabelAttribute(): string
    {
        return match (true) {
            $this->overall_rating >= 5 => 'Excellent',
            $this->overall_rating >= 4 => 'Very Good',
            $this->overall_rating >= 3 => 'Good',
            $this->overall_rating >= 2 => 'Fair',
            default => 'Poor',
        };
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePendingModeration($query)
    {
        return $query->where('moderation_status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('moderation_status', 'approved');
    }

    public function scopeForHotel($query, int $hotelId)
    {
        return $query->where('hotel_id', $hotelId);
    }

    public function scopeWithMinRating($query, int $minRating)
    {
        return $query->where('overall_rating', '>=', $minRating);
    }

    // Methods
    public function approve(?int $userId = null): void
    {
        $this->update([
            'moderation_status' => 'approved',
            'is_published' => true,
            'moderated_by' => $userId ?? auth()->id(),
            'moderated_at' => now(),
        ]);

        $this->hotel->updateReviewStats();
    }

    public function reject(string $notes, ?int $userId = null): void
    {
        $this->update([
            'moderation_status' => 'rejected',
            'is_published' => false,
            'moderation_notes' => $notes,
            'moderated_by' => $userId ?? auth()->id(),
            'moderated_at' => now(),
        ]);
    }

    public function addHotelResponse(string $response, ?int $userId = null): void
    {
        $this->update([
            'hotel_response' => $response,
            'hotel_responded_at' => now(),
            'hotel_responded_by' => $userId ?? auth()->id(),
        ]);
    }

    public function markHelpful(): void
    {
        $this->increment('helpful_count');
    }

    public function markNotHelpful(): void
    {
        $this->increment('not_helpful_count');
    }

    public function report(): void
    {
        $this->increment('reported_count');
    }
}
