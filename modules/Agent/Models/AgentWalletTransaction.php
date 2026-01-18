<?php

namespace Modules\Agent\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Support\Models\BaseModel;

class AgentWalletTransaction extends BaseModel
{
    use HasFactory;

    protected $table = 'agent_wallet_transactions';

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'details' => 'array',
        'approved_at' => 'datetime',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDeposits($query)
    {
        return $query->where('type', 'deposit');
    }
}
