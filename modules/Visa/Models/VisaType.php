<?php

namespace Modules\Visa\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;
use Modules\Visa\Database\Factories\VisaTypeFactory;

class VisaType extends BaseModel
{
    use ActivityLog, HasFactory, Searchable, SoftDeletes;

    protected $table = 'visa_types';

    protected static function newFactory(): VisaTypeFactory
    {
        return VisaTypeFactory::new();
    }

    protected function casts(): array
    {
        return [
            'eligible_nationalities' => 'array',
            'restricted_nationalities' => 'array',
            'is_extendable' => 'boolean',
            'is_convertible' => 'boolean',
            'work_permitted' => 'boolean',
            'study_permitted' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the country.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(VisaCountry::class, 'country_id');
    }

    /**
     * Get the category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(VisaCategory::class, 'category_id');
    }

    /**
     * Get visas of this type.
     */
    public function visas(): HasMany
    {
        return $this->hasMany(Visa::class, 'visa_type_id');
    }

    /**
     * Scope active types.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get formatted validity.
     */
    public function getFormattedValidityAttribute(): string
    {
        if ($this->validity_days) {
            return $this->validity_days.' days';
        }

        return 'N/A';
    }

    /**
     * Get formatted stay duration.
     */
    public function getFormattedStayAttribute(): string
    {
        if ($this->stay_days) {
            return $this->stay_days.' days';
        }

        return 'N/A';
    }
}
