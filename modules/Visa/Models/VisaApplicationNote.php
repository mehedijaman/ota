<?php

namespace Modules\Visa\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;

class VisaApplicationNote extends BaseModel
{
    use ActivityLog, SoftDeletes;

    protected $table = 'visa_application_notes';

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'is_important' => 'boolean',
            'is_visible_to_customer' => 'boolean',
            'is_visible_to_agent' => 'boolean',
            'notify_customer' => 'boolean',
            'notify_agent' => 'boolean',
            'notified_at' => 'datetime',
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
     * Get the creator.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope internal notes.
     */
    public function scopeInternal($query)
    {
        return $query->where('type', 'internal');
    }

    /**
     * Scope pinned notes.
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Scope important notes.
     */
    public function scopeImportant($query)
    {
        return $query->where('is_important', true);
    }

    /**
     * Scope notes visible to customer.
     */
    public function scopeVisibleToCustomer($query)
    {
        return $query->where('is_visible_to_customer', true);
    }

    /**
     * Scope notes visible to agent.
     */
    public function scopeVisibleToAgent($query)
    {
        return $query->where('is_visible_to_agent', true);
    }
}
