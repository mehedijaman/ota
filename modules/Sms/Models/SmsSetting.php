<?php

namespace Modules\Sms\Models;

use Modules\Support\Models\BaseModel;

class SmsSetting extends BaseModel
{
    protected $table = 'sms_settings';

    protected $fillable = [
        'provider_class',
        'credentials',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'credentials' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];
}
