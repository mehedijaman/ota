<?php

namespace Modules\Visa\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Support\Models\BaseModel;

class VisaApplicationChecklistItem extends BaseModel
{
    protected $table = 'visa_application_checklist_items';

    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
            'completed_at' => 'datetime',
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
     * Get the checklist item.
     */
    public function checklistItem(): BelongsTo
    {
        return $this->belongsTo(VisaChecklistItem::class, 'checklist_item_id');
    }

    /**
     * Get completed by user.
     */
    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /**
     * Scope completed items.
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    /**
     * Scope incomplete items.
     */
    public function scopeIncomplete($query)
    {
        return $query->where('is_completed', false);
    }
}
