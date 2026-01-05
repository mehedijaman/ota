<?php

namespace Modules\Sms\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Support\Models\BaseModel;
use Modules\Tenant\Models\Tenant;

class SmsPackagePurchase extends BaseModel
{
    protected $table = 'sms_package_purchases';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'sms_package_id',
        'quantity',
        'total_credits',
        'total_price',
        'currency',
        'status',
        'payment_method',
        'payment_reference',
        'payment_notes',
        'transaction_id',
        'payment_status',
        'gateway_transaction_id',
        'payment_details',
        'gateway_response',
        'paid_at',
        'starts_at',
        'expires_at',
        'approved_by',
        'approved_at',
        'admin_notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'total_credits' => 'integer',
        'total_price' => 'decimal:2',
        'payment_details' => 'array',
        'gateway_response' => 'array',
        'paid_at' => 'datetime',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(SmsPackage::class, 'sms_package_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
