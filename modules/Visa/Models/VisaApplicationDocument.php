<?php

namespace Modules\Visa\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Support\Models\BaseModel;

class VisaApplicationDocument extends BaseModel
{
    protected $table = 'visa_application_documents';

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'is_latest' => 'boolean',
            'document_issue_date' => 'date',
            'document_expiry_date' => 'date',
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
     * Get the traveler.
     */
    public function traveler(): BelongsTo
    {
        return $this->belongsTo(VisaApplicationTraveler::class, 'traveler_id');
    }

    /**
     * Get the requirement.
     */
    public function requirement(): BelongsTo
    {
        return $this->belongsTo(VisaRequirement::class, 'requirement_id');
    }

    /**
     * Get verified by user.
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get uploaded by user.
     */
    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get formatted file size.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2).' GB';
        }

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2).' KB';
        }

        return $bytes.' bytes';
    }

    /**
     * Check if document is expired.
     */
    public function getIsExpiredAttribute(): bool
    {
        if (! $this->document_expiry_date) {
            return false;
        }

        return $this->document_expiry_date->isPast();
    }

    /**
     * Scope by status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope pending documents.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope approved documents.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope latest versions only.
     */
    public function scopeLatest($query)
    {
        return $query->where('is_latest', true);
    }

    /**
     * Scope by document type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('document_type', $type);
    }
}
