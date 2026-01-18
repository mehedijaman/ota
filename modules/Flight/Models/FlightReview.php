<?php

namespace Modules\Flight\Models;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Customer\Models\Customer;
use Modules\Flight\Database\Factories\FlightReviewFactory;
use Modules\Support\Models\BaseModel;

class FlightReview extends BaseModel
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $table = 'flight_reviews';

    protected function casts(): array
    {
        return [
            'overall_rating' => 'integer',
            'comfort_rating' => 'integer',
            'staff_rating' => 'integer',
            'food_rating' => 'integer',
            'entertainment_rating' => 'integer',
            'punctuality_rating' => 'integer',
            'value_rating' => 'integer',
            'cleanliness_rating' => 'integer',
            'checkin_rating' => 'integer',
            'travel_date' => 'date',
            'helpful_count' => 'integer',
            'not_helpful_count' => 'integer',
            'is_approved' => 'boolean',
            'is_featured' => 'boolean',
            'is_verified_purchase' => 'boolean',
            'approved_at' => 'datetime',
            'is_flagged' => 'boolean',
            'admin_responded_at' => 'datetime',
        ];
    }

    protected static function newFactory(): FlightReviewFactory
    {
        return FlightReviewFactory::new();
    }

    // Relationships
    public function booking(): BelongsTo
    {
        return $this->belongsTo(FlightBooking::class, 'booking_id');
    }

    public function airline(): BelongsTo
    {
        return $this->belongsTo(FlightAirline::class, 'airline_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function adminRespondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_responded_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getReviewerDisplayNameAttribute(): string
    {
        if ($this->customer) {
            return $this->customer->name;
        }

        return $this->reviewer_name ?? 'Anonymous';
    }

    public function getRatingDisplayAttribute(): string
    {
        return str_repeat('★', $this->overall_rating).str_repeat('☆', 5 - $this->overall_rating);
    }

    public function getAverageDetailRatingAttribute(): float
    {
        $ratings = array_filter([
            $this->comfort_rating,
            $this->staff_rating,
            $this->food_rating,
            $this->entertainment_rating,
            $this->punctuality_rating,
            $this->value_rating,
            $this->cleanliness_rating,
            $this->checkin_rating,
        ]);

        if (empty($ratings)) {
            return $this->overall_rating;
        }

        return round(array_sum($ratings) / count($ratings), 1);
    }

    public function getTravelClassDisplayAttribute(): string
    {
        return match ($this->travel_class) {
            'economy' => 'Economy',
            'premium_economy' => 'Premium Economy',
            'business' => 'Business',
            'first' => 'First',
            default => ucfirst($this->travel_class),
        };
    }

    public function getTravelerTypeDisplayAttribute(): string
    {
        return match ($this->traveler_type) {
            'solo' => 'Solo Traveler',
            'couple' => 'Couple',
            'family' => 'Family',
            'business' => 'Business Traveler',
            'group' => 'Group',
            default => ucfirst($this->traveler_type),
        };
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified_purchase', true);
    }

    public function scopeFlagged($query)
    {
        return $query->where('is_flagged', true);
    }

    public function scopeForAirline($query, int $airlineId)
    {
        return $query->where('airline_id', $airlineId);
    }

    public function scopeWithRating($query, int $rating)
    {
        return $query->where('overall_rating', $rating);
    }

    public function scopeMinRating($query, int $minRating)
    {
        return $query->where('overall_rating', '>=', $minRating);
    }

    public function scopeMostHelpful($query)
    {
        return $query->orderByDesc('helpful_count');
    }

    public function scopeRecent($query)
    {
        return $query->orderByDesc('created_at');
    }

    // Methods
    public function isApproved(): bool
    {
        return (bool) $this->is_approved;
    }

    public function isFlagged(): bool
    {
        return (bool) $this->is_flagged;
    }

    public function approve(?int $userId = null): void
    {
        $this->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => $userId ?? auth()->id(),
            'is_flagged' => false,
        ]);

        $this->airline?->updateReviewStats();
    }

    public function flag(string $reason): void
    {
        $this->update([
            'is_flagged' => true,
            'flag_reason' => $reason,
        ]);
    }

    public function incrementHelpful(): void
    {
        $this->increment('helpful_count');
    }

    public function incrementNotHelpful(): void
    {
        $this->increment('not_helpful_count');
    }

    public function addAdminResponse(string $response, ?int $userId = null): void
    {
        $this->update([
            'admin_response' => $response,
            'admin_responded_at' => now(),
            'admin_responded_by' => $userId ?? auth()->id(),
        ]);
    }
}
