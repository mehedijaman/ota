<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Support\Models\BaseModel;

class HotelBookingGuest extends BaseModel
{
    use HasFactory;

    protected $table = 'hotel_booking_guests';

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'is_lead_guest' => 'boolean',
            'date_of_birth' => 'date',
            'age' => 'integer',
            'id_issue_date' => 'date',
            'id_expiry_date' => 'date',
            'needs_wheelchair' => 'boolean',
        ];
    }

    public function bookingRoom(): BelongsTo
    {
        return $this->belongsTo(HotelBookingRoom::class, 'booking_room_id');
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([
            $this->title,
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ])));
    }

    public function getDisplayNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function getCalculatedAgeAttribute(): ?int
    {
        if (! $this->date_of_birth) {
            return $this->age;
        }

        return $this->date_of_birth->age;
    }

    public function getGuestTypeLabelAttribute(): string
    {
        return match ($this->guest_type) {
            'adult' => 'Adult',
            'child' => 'Child',
            'infant' => 'Infant',
            default => ucfirst($this->guest_type ?? 'Guest'),
        };
    }

    public function getIdTypeLabelAttribute(): ?string
    {
        if (! $this->id_type) {
            return null;
        }

        return match ($this->id_type) {
            'passport' => 'Passport',
            'nid' => 'National ID',
            'driving_license' => 'Driving License',
            'national_id' => 'National ID',
            'other' => 'Other ID',
            default => ucfirst(str_replace('_', ' ', $this->id_type)),
        };
    }

    public function isIdExpiringSoon(int $days = 180): bool
    {
        if (! $this->id_expiry_date) {
            return false;
        }

        return $this->id_expiry_date->diffInDays(now()) <= $days;
    }

    public function isIdExpired(): bool
    {
        if (! $this->id_expiry_date) {
            return false;
        }

        return $this->id_expiry_date->isPast();
    }

    // Scopes
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeLeadGuests($query)
    {
        return $query->where('is_lead_guest', true);
    }

    public function scopeAdults($query)
    {
        return $query->where('guest_type', 'adult');
    }

    public function scopeChildren($query)
    {
        return $query->where('guest_type', 'child');
    }

    public function scopeInfants($query)
    {
        return $query->where('guest_type', 'infant');
    }
}
