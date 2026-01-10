<?php

namespace Modules\Tour\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Tour\Database\Factories\TourBookingFactory;

class TourBooking extends BaseModel
{
    use ActivityLog, HasFactory, Searchable, SoftDeletes;

    protected $table = 'tour_bookings';

    protected static function newFactory()
    {
        return TourBookingFactory::new();
    }

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'extras_total' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'deposit_paid' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'amount_due' => 'decimal:2',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
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
     * Get the pricing tier.
     */
    /**
     * Get the pricing tier.
     */
    public function pricing(): BelongsTo
    {
        return $this->belongsTo(TourPricing::class, 'pricing_id');
    }

    /**
     * Get the customer.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(\Modules\Customer\Models\Customer::class);
    }

    /**
     * Get the creator (user).
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get travelers.
     */
    public function travelers(): HasMany
    {
        return $this->hasMany(TourBookingTraveler::class, 'booking_id');
    }

    /**
     * Get the lead traveler.
     */
    public function leadTraveler(): HasOne
    {
        return $this->hasOne(TourBookingTraveler::class, 'booking_id')->where('is_lead', true);
    }

    /**
     * Get payments.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(TourBookingPayment::class, 'booking_id');
    }

    /**
     * Get extras.
     */
    public function extras(): HasMany
    {
        return $this->hasMany(TourBookingExtra::class, 'booking_id');
    }

    /**
     * Get cancellation.
     */
    public function cancellation(): HasOne
    {
        return $this->hasOne(TourBookingCancellation::class, 'booking_id');
    }

    /**
     * Get review.
     */
    public function review(): HasOne
    {
        return $this->hasOne(TourReview::class, 'booking_id');
    }

    /**
     * Get total travelers count.
     */
    public function getTotalTravelersAttribute(): int
    {
        return $this->adult_count + $this->child_count + $this->infant_count;
    }

    /**
     * Check if booking is paid.
     */
    public function getIsPaidAttribute(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Scope pending bookings.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope confirmed bookings.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope cancelled bookings.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Generate unique booking number.
     */
    public static function generateBookingNumber(): string
    {
        do {
            $number = 'TB'.date('Ymd').strtoupper(substr(uniqid(), -6));
        } while (self::where('booking_number', $number)->exists());

        return $number;
    }
}
