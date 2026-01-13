<?php

namespace Modules\Visa\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Visa\Database\Factories\VisaFaqFactory;

class VisaFaq extends BaseModel
{
    use ActivityLog, HasFactory;

    protected $table = 'visa_faqs';

    protected static function newFactory(): VisaFaqFactory
    {
        return VisaFaqFactory::new();
    }

    protected function casts(): array
    {
        return [
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
     * Scope active FAQs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
