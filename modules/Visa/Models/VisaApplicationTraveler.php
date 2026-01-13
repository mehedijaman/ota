<?php

namespace Modules\Visa\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Support\Models\BaseModel;
use Modules\Visa\Database\Factories\VisaApplicationTravelerFactory;

class VisaApplicationTraveler extends BaseModel
{
    use HasFactory;

    protected $table = 'visa_application_travelers';

    protected static function newFactory(): VisaApplicationTravelerFactory
    {
        return VisaApplicationTravelerFactory::new();
    }

    protected function casts(): array
    {
        return [
            'countries_visited' => 'array',
            'additional_data' => 'array',
            'date_of_birth' => 'date',
            'passport_issue_date' => 'date',
            'passport_expiry_date' => 'date',
            'old_passport_expiry' => 'date',
            'employment_start_date' => 'date',
            'course_start_date' => 'date',
            'course_end_date' => 'date',
            'spouse_date_of_birth' => 'date',
            'monthly_income' => 'decimal:2',
            'bank_balance' => 'decimal:2',
            'is_lead' => 'boolean',
            'has_previous_visa' => 'boolean',
            'has_visa_refusal' => 'boolean',
            'has_deportation_history' => 'boolean',
            'has_criminal_record' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Get the application.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(VisaApplication::class, 'application_id');
    }

    /**
     * Get documents for this traveler.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(VisaApplicationDocument::class, 'traveler_id');
    }

    /**
     * Get verified by user.
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get full name attribute.
     */
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

    /**
     * Get age attribute.
     */
    public function getAgeAttribute(): ?int
    {
        if (! $this->date_of_birth) {
            return null;
        }

        return $this->date_of_birth->age;
    }

    /**
     * Check if passport is expired.
     */
    public function getIsPassportExpiredAttribute(): bool
    {
        if (! $this->passport_expiry_date) {
            return false;
        }

        return $this->passport_expiry_date->isPast();
    }

    /**
     * Check if passport expires within 6 months.
     */
    public function getIsPassportExpiringSoonAttribute(): bool
    {
        if (! $this->passport_expiry_date) {
            return false;
        }

        return $this->passport_expiry_date->isBefore(now()->addMonths(6));
    }

    /**
     * Scope lead travelers.
     */
    public function scopeLead($query)
    {
        return $query->where('is_lead', true);
    }

    /**
     * Scope by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope verified travelers.
     */
    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }
}
