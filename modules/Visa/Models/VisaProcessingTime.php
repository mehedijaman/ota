<?php

namespace Modules\Visa\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Visa\Database\Factories\VisaProcessingTimeFactory;

class VisaProcessingTime extends BaseModel
{
    use ActivityLog, HasFactory;

    protected $table = 'visa_processing_times';

    protected static function newFactory(): VisaProcessingTimeFactory
    {
        return VisaProcessingTimeFactory::new();
    }

    protected function casts(): array
    {
        return [
            'additional_fee' => 'decimal:2',
            'is_guaranteed' => 'boolean',
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
     * Scope active processing times.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get processing time range attribute.
     */
    public function getProcessingRangeAttribute(): string
    {
        if ($this->processing_days_min === $this->processing_days_max) {
            return $this->processing_days_min.' days';
        }

        return $this->processing_days_min.'-'.$this->processing_days_max.' days';
    }
}
