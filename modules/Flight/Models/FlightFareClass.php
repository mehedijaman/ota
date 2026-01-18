<?php

namespace Modules\Flight\Models;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Flight\Database\Factories\FlightFareClassFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\Searchable;

class FlightFareClass extends BaseModel
{
    use HasFactory, HasUuid, Searchable;

    protected $table = 'flight_fare_classes';

    protected function casts(): array
    {
        return [
            'checked_bags_included' => 'integer',
            'carry_on_bags_included' => 'integer',
            'checked_bag_weight_kg' => 'integer',
            'carry_on_weight_kg' => 'integer',
            'baggage_details' => 'array',
            'is_refundable' => 'boolean',
            'is_changeable' => 'boolean',
            'is_upgradable' => 'boolean',
            'change_fee' => 'decimal:2',
            'cancellation_fee' => 'decimal:2',
            'no_show_fee' => 'decimal:2',
            'change_allowed_until_hours' => 'integer',
            'cancel_allowed_until_hours' => 'integer',
            'seat_selection_free' => 'boolean',
            'priority_boarding' => 'boolean',
            'lounge_access' => 'boolean',
            'fast_track_security' => 'boolean',
            'extra_legroom_available' => 'boolean',
            'mileage_earning_percent' => 'integer',
            'additional_benefits' => 'array',
            'min_advance_booking_hours' => 'integer',
            'max_advance_booking_days' => 'integer',
            'min_stay_days' => 'integer',
            'max_stay_days' => 'integer',
            'requires_round_trip' => 'boolean',
            'display_order' => 'integer',
            'terms_conditions' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function newFactory(): FlightFareClassFactory
    {
        return FlightFareClassFactory::new();
    }

    // Relationships
    public function airline(): BelongsTo
    {
        return $this->belongsTo(FlightAirline::class, 'airline_id');
    }

    public function fares(): HasMany
    {
        return $this->hasMany(FlightFare::class, 'fare_class_id');
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
    public function getDisplayNameAttribute(): string
    {
        $airline = $this->airline?->iata_code ?? '';

        return $airline ? "{$airline} - {$this->name}" : $this->name;
    }

    public function getCabinDisplayAttribute(): string
    {
        return match ($this->cabin_class) {
            'first' => 'First Class',
            'business' => 'Business Class',
            'premium_economy' => 'Premium Economy',
            'economy' => 'Economy',
            default => ucfirst($this->cabin_class),
        };
    }

    public function getBaggageDisplayAttribute(): string
    {
        if ($this->checked_bags_included > 0) {
            return "{$this->checked_bags_included} bag(s) × {$this->checked_bag_weight_kg}kg";
        }

        return 'No checked baggage';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForAirline($query, int $airlineId)
    {
        return $query->where('airline_id', $airlineId);
    }

    public function scopeGeneric($query)
    {
        return $query->whereNull('airline_id');
    }

    public function scopeOfCabin($query, string $cabin)
    {
        return $query->where('cabin_class', $cabin);
    }

    public function scopeRefundable($query)
    {
        return $query->where('is_refundable', true);
    }

    public function scopeByDisplayOrder($query)
    {
        return $query->orderBy('display_order');
    }

    // Methods
    public function isFlexible(): bool
    {
        return $this->is_refundable && $this->is_changeable;
    }

    public function hasBaggageIncluded(): bool
    {
        return $this->checked_bags_included > 0;
    }

    public function hasLounge(): bool
    {
        return (bool) $this->lounge_access;
    }
}
