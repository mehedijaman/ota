<?php

namespace Modules\Flight\Models;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Flight\Database\Factories\FlightFaqFactory;
use Modules\Support\Models\BaseModel;

class FlightFaq extends BaseModel
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $table = 'flight_faqs';

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'views_count' => 'integer',
            'helpful_count' => 'integer',
            'not_helpful_count' => 'integer',
        ];
    }

    protected static function newFactory(): FlightFaqFactory
    {
        return FlightFaqFactory::new();
    }

    // Relationships
    public function airline(): BelongsTo
    {
        return $this->belongsTo(FlightAirline::class, 'airline_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Accessors
    public function getCategoryDisplayAttribute(): string
    {
        return match ($this->category) {
            'general' => 'General',
            'booking' => 'Booking',
            'payment' => 'Payment',
            'baggage' => 'Baggage',
            'checkin' => 'Check-in',
            'cancellation' => 'Cancellation',
            'refund' => 'Refund',
            'exchange' => 'Exchange',
            'visa' => 'Visa & Travel Documents',
            'special_assistance' => 'Special Assistance',
            'infants_children' => 'Infants & Children',
            'pets' => 'Pets',
            'loyalty_program' => 'Loyalty Program',
            'covid' => 'COVID-19',
            default => ucwords(str_replace('_', ' ', $this->category)),
        };
    }

    public function getHelpfulnessRatioAttribute(): float
    {
        $total = $this->helpful_count + $this->not_helpful_count;
        if ($total === 0) {
            return 0;
        }

        return round($this->helpful_count / $total * 100, 1);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeGeneral($query)
    {
        return $query->whereNull('airline_id');
    }

    public function scopeForAirline($query, int $airlineId)
    {
        return $query->where('airline_id', $airlineId);
    }

    public function scopeOfCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByOrder($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopePopular($query)
    {
        return $query->orderByDesc('views_count');
    }

    // Methods
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function incrementHelpful(): void
    {
        $this->increment('helpful_count');
    }

    public function incrementNotHelpful(): void
    {
        $this->increment('not_helpful_count');
    }
}
