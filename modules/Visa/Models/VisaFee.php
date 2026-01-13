<?php

namespace Modules\Visa\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Visa\Database\Factories\VisaFeeFactory;

class VisaFee extends BaseModel
{
    use ActivityLog, HasFactory;

    protected $table = 'visa_fees';

    protected static function newFactory(): VisaFeeFactory
    {
        return VisaFeeFactory::new();
    }

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'is_per_person' => 'boolean',
            'is_refundable' => 'boolean',
            'is_included_in_package' => 'boolean',
            'is_optional' => 'boolean',
            'is_active' => 'boolean',
            'valid_from' => 'date',
            'valid_to' => 'date',
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
     * Scope active fees.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by fee type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('fee_type', $type);
    }

    /**
     * Scope currently valid fees.
     */
    public function scopeCurrentlyValid($query)
    {
        $now = now()->toDateString();

        return $query->where(function ($q) use ($now) {
            $q->whereNull('valid_from')
                ->orWhere('valid_from', '<=', $now);
        })->where(function ($q) use ($now) {
            $q->whereNull('valid_to')
                ->orWhere('valid_to', '>=', $now);
        });
    }
}
