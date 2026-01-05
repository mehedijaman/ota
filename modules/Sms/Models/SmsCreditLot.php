<?php

namespace Modules\Sms\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Support\Models\BaseModel;

class SmsCreditLot extends BaseModel
{
    protected $table = 'sms_credit_lots';

    protected $fillable = [
        'tenant_id',
        'sms_package_purchase_id',
        'credits_total',
        'credits_used',
        'credits_reserved',
        'expires_at',
    ];

    protected $casts = [
        'credits_total' => 'integer',
        'credits_used' => 'integer',
        'credits_reserved' => 'integer',
        'expires_at' => 'datetime',
    ];

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(SmsPackagePurchase::class, 'sms_package_purchase_id');
    }
}
