<?php

namespace Modules\Sms\Models;

use Modules\Support\Models\BaseModel;

class SmsPackage extends BaseModel
{
    protected $table = 'sms_packages';

    protected $fillable = [
        'name',
        'description',
        'sms_credits',
        'duration_days',
        'price',
        'currency',
        'is_active',
    ];

    protected $casts = [
        'sms_credits' => 'integer',
        'duration_days' => 'integer',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
