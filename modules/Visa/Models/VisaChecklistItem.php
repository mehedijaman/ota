<?php

namespace Modules\Visa\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Support\Models\BaseModel;

class VisaChecklistItem extends BaseModel
{
    protected $table = 'visa_checklist_items';

    protected function casts(): array
    {
        return [
            'is_mandatory' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the checklist.
     */
    public function checklist(): BelongsTo
    {
        return $this->belongsTo(VisaChecklist::class, 'checklist_id');
    }

    /**
     * Get application checklist items.
     */
    public function applicationItems(): HasMany
    {
        return $this->hasMany(VisaApplicationChecklistItem::class, 'checklist_item_id');
    }

    /**
     * Scope active items.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope mandatory items.
     */
    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }
}
