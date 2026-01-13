<?php

namespace Modules\Visa\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;
use Modules\Visa\Database\Factories\VisaApplicationFactory;

class VisaApplication extends BaseModel
{
    use ActivityLog, HasFactory, Searchable, SoftDeletes;

    protected $table = 'visa_applications';

    protected static function newFactory(): VisaApplicationFactory
    {
        return VisaApplicationFactory::new();
    }

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'embassy_fees' => 'decimal:2',
            'service_fees' => 'decimal:2',
            'express_fees' => 'decimal:2',
            'additional_fees' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'amount_due' => 'decimal:2',
            'intended_travel_date' => 'date',
            'intended_return_date' => 'date',
            'documents_submitted_date' => 'date',
            'embassy_submission_date' => 'date',
            'expected_result_date' => 'date',
            'passport_collection_date' => 'date',
            'visa_issue_date' => 'date',
            'visa_expiry_date' => 'date',
            'interview_date' => 'datetime',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Get the visa.
     */
    public function visa(): BelongsTo
    {
        return $this->belongsTo(Visa::class);
    }

    /**
     * Get the country.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(VisaCountry::class, 'country_id');
    }

    /**
     * Get the processing time option.
     */
    public function processingTime(): BelongsTo
    {
        return $this->belongsTo(VisaProcessingTime::class, 'processing_time_id');
    }

    /**
     * Get the customer.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(\Modules\Customer\Models\Customer::class);
    }

    /**
     * Get the agent.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(\Modules\Agent\Models\Agent::class);
    }

    /**
     * Get the assigned user.
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get travelers.
     */
    public function travelers(): HasMany
    {
        return $this->hasMany(VisaApplicationTraveler::class, 'application_id');
    }

    /**
     * Get the lead traveler.
     */
    public function leadTraveler(): HasOne
    {
        return $this->hasOne(VisaApplicationTraveler::class, 'application_id')->where('is_lead', true);
    }

    /**
     * Get documents.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(VisaApplicationDocument::class, 'application_id');
    }

    /**
     * Get payments.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(VisaApplicationPayment::class, 'application_id');
    }

    /**
     * Get notes.
     */
    public function notes(): HasMany
    {
        return $this->hasMany(VisaApplicationNote::class, 'application_id')->orderByDesc('created_at');
    }

    /**
     * Get status history.
     */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(VisaApplicationStatusHistory::class, 'application_id')->orderByDesc('created_at');
    }

    /**
     * Get checklist items.
     */
    public function checklistItems(): HasMany
    {
        return $this->hasMany(VisaApplicationChecklistItem::class, 'application_id');
    }

    /**
     * Get review.
     */
    public function review(): HasOne
    {
        return $this->hasOne(VisaReview::class, 'application_id');
    }

    /**
     * Get the creator.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Alias for creator relationship.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get total travelers count.
     */
    public function getTotalTravelersAttribute(): int
    {
        return $this->adult_count + $this->child_count + $this->infant_count;
    }

    /**
     * Check if application is paid.
     */
    public function getIsPaidAttribute(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if application is pending.
     */
    public function getIsPendingAttribute(): bool
    {
        return in_array($this->status, ['draft', 'pending', 'documents_pending']);
    }

    /**
     * Check if application is processing.
     */
    public function getIsProcessingAttribute(): bool
    {
        return in_array($this->status, ['documents_submitted', 'under_review', 'processing', 'submitted_to_embassy', 'interview_scheduled', 'interview_completed']);
    }

    /**
     * Check if application is completed.
     */
    public function getIsCompletedAttribute(): bool
    {
        return in_array($this->status, ['approved', 'completed']);
    }

    /**
     * Check if application is cancelled or rejected.
     */
    public function getIsClosedAttribute(): bool
    {
        return in_array($this->status, ['rejected', 'cancelled']);
    }

    /**
     * Scope by status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope pending applications.
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'pending', 'documents_pending']);
    }

    /**
     * Scope processing applications.
     */
    public function scopeProcessing($query)
    {
        return $query->whereIn('status', ['documents_submitted', 'under_review', 'processing', 'submitted_to_embassy', 'interview_scheduled', 'interview_completed']);
    }

    /**
     * Scope completed applications.
     */
    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['approved', 'completed']);
    }
}
