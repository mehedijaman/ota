<?php

namespace Modules\Flight\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Flight\Database\Factories\FlightBookingPassengerFactory;
use Modules\Support\Models\BaseModel;
use Nnjeim\World\Models\Country;

class FlightBookingPassenger extends BaseModel
{
    use HasFactory, HasUuid;

    protected $table = 'flight_booking_passengers';

    protected function casts(): array
    {
        return [
            'passenger_number' => 'integer',
            'is_lead_passenger' => 'boolean',
            'date_of_birth' => 'date',
            'passport_issue_date' => 'date',
            'passport_expiry_date' => 'date',
            'ticketed_at' => 'datetime',
            'special_service_requests' => 'array',
            'requires_wheelchair' => 'boolean',
            'requires_assistance' => 'boolean',
            'is_unaccompanied_minor' => 'boolean',
            'has_medical_condition' => 'boolean',
            'requires_oxygen' => 'boolean',
            'traveling_with_pet' => 'boolean',
            'base_fare' => 'decimal:2',
            'tax' => 'decimal:2',
            'fee' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    protected static function newFactory(): FlightBookingPassengerFactory
    {
        return FlightBookingPassengerFactory::new();
    }

    // Relationships
    public function booking(): BelongsTo
    {
        return $this->belongsTo(FlightBooking::class, 'booking_id');
    }

    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'nationality_id');
    }

    public function passportCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'passport_country_id');
    }

    public function associatedAdult(): BelongsTo
    {
        return $this->belongsTo(self::class, 'associated_adult_id');
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->title,
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ]);

        return implode(' ', $parts);
    }

    public function getNameWithTypeAttribute(): string
    {
        return "{$this->full_name} ({$this->passenger_type_display})";
    }

    public function getPassengerTypeDisplayAttribute(): string
    {
        return match ($this->passenger_type) {
            'adult' => 'Adult',
            'child' => 'Child',
            'infant' => 'Infant',
            default => ucfirst($this->passenger_type),
        };
    }

    public function getAgeAttribute(): ?int
    {
        if (! $this->date_of_birth) {
            return null;
        }

        return $this->date_of_birth->age;
    }

    public function getTicketStatusDisplayAttribute(): string
    {
        return match ($this->ticket_status) {
            'not_issued' => 'Not Issued',
            'issued' => 'Issued',
            'void' => 'Voided',
            'refunded' => 'Refunded',
            'exchanged' => 'Exchanged',
            'used' => 'Used',
            default => ucfirst($this->ticket_status),
        };
    }

    public function getPassportExpiryStatusAttribute(): string
    {
        if (! $this->passport_expiry_date) {
            return 'unknown';
        }

        $daysUntilExpiry = now()->diffInDays($this->passport_expiry_date, false);

        if ($daysUntilExpiry < 0) {
            return 'expired';
        }
        if ($daysUntilExpiry < 180) {
            return 'expiring_soon';
        }

        return 'valid';
    }

    public function getMealPreferenceDisplayAttribute(): string
    {
        return match ($this->meal_preference) {
            'standard' => 'Standard',
            'vegetarian' => 'Vegetarian (VGML)',
            'vegan' => 'Vegan (VGON)',
            'halal' => 'Halal (MOML)',
            'kosher' => 'Kosher (KSML)',
            'gluten_free' => 'Gluten Free (GFML)',
            'diabetic' => 'Diabetic (DBML)',
            'child_meal' => 'Child Meal (CHML)',
            'infant_meal' => 'Infant Meal (BBML)',
            default => ucwords(str_replace('_', ' ', $this->meal_preference)),
        };
    }

    // Scopes
    public function scopeAdults($query)
    {
        return $query->where('passenger_type', 'adult');
    }

    public function scopeChildren($query)
    {
        return $query->where('passenger_type', 'child');
    }

    public function scopeInfants($query)
    {
        return $query->where('passenger_type', 'infant');
    }

    public function scopeLead($query)
    {
        return $query->where('is_lead_passenger', true);
    }

    public function scopeTicketed($query)
    {
        return $query->where('ticket_status', 'issued');
    }

    public function scopeNotTicketed($query)
    {
        return $query->where('ticket_status', 'not_issued');
    }

    public function scopeRequiringAssistance($query)
    {
        return $query->where('requires_wheelchair', true)
            ->orWhere('requires_assistance', true);
    }

    // Methods
    public function isAdult(): bool
    {
        return $this->passenger_type === 'adult';
    }

    public function isChild(): bool
    {
        return $this->passenger_type === 'child';
    }

    public function isInfant(): bool
    {
        return $this->passenger_type === 'infant';
    }

    public function isTicketed(): bool
    {
        return $this->ticket_status === 'issued';
    }

    public function hasValidPassport(): bool
    {
        if (! $this->passport_expiry_date) {
            return false;
        }

        return $this->passport_expiry_date->isFuture()
            && $this->passport_expiry_date->diffInDays(now()) >= 180;
    }

    public function requiresSpecialAssistance(): bool
    {
        return $this->requires_wheelchair
            || $this->requires_assistance
            || $this->is_unaccompanied_minor
            || $this->requires_oxygen;
    }
}
