<?php

namespace Modules\Visa\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Support\Models\BaseModel;

class VisaApplicationStatusHistory extends BaseModel
{
    protected $table = 'visa_application_status_histories';

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'is_visible_to_customer' => 'boolean',
            'notify_customer' => 'boolean',
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
     * Get the user who made the change.
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Scope visible to customer.
     */
    public function scopeVisibleToCustomer($query)
    {
        return $query->where('is_visible_to_customer', true);
    }

    /**
     * Scope by status.
     */
    public function scopeToStatus($query, string $status)
    {
        return $query->where('to_status', $status);
    }
}
