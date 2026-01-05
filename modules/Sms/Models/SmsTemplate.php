<?php

namespace Modules\Sms\Models;

use Modules\Support\Models\BaseModel;

class SmsTemplate extends BaseModel
{
    protected $table = 'sms_templates';

    protected $fillable = [
        'name',
        'content',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
