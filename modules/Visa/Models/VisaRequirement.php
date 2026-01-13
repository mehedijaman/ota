<?php

namespace Modules\Visa\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Visa\Database\Factories\VisaRequirementFactory;

class VisaRequirement extends BaseModel
{
    use ActivityLog, HasFactory;

    protected $table = 'visa_requirements';

    protected static function newFactory(): VisaRequirementFactory
    {
        return VisaRequirementFactory::new();
    }

    protected function casts(): array
    {
        return [
            'accepted_formats' => 'array',
            'is_mandatory' => 'boolean',
            'needs_upload' => 'boolean',
            'needs_original' => 'boolean',
            'needs_notarization' => 'boolean',
            'needs_translation' => 'boolean',
            'needs_attestation' => 'boolean',
            'is_active' => 'boolean',
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
     * Scope active requirements.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope mandatory requirements.
     */
    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    /**
     * Scope by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get accepted formats as string.
     */
    public function getAcceptedFormatsStringAttribute(): string
    {
        if (empty($this->accepted_formats)) {
            return '';
        }

        return implode(', ', $this->accepted_formats);
    }
}
