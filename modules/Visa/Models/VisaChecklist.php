<?php

namespace Modules\Visa\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;

class VisaChecklist extends BaseModel
{
    use ActivityLog;

    protected $table = 'visa_checklists';

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
     * Get checklist items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(VisaChecklistItem::class, 'checklist_id')->orderBy('sort_order');
    }

    /**
     * Get active items.
     */
    public function activeItems(): HasMany
    {
        return $this->items()->where('is_active', true);
    }

    /**
     * Scope active checklists.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by stage.
     */
    public function scopeForStage($query, string $stage)
    {
        return $query->where('stage', $stage);
    }
}
