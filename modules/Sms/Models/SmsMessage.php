<?php

namespace Modules\Sms\Models;

use Modules\Support\Models\BaseModel;

class SmsMessage extends BaseModel
{
    protected $table = 'sms_messages';

    protected $fillable = [
        'user_id',
        'type',
        'to_phone',
        'message',
        'parts',
        'cost_credits',
        'status',
        'provider',
        'provider_message_id',
        'provider_response',
        'error_message',
        'queued_at',
        'sent_at',
        'meta',
    ];

    protected $casts = [
        'parts' => 'integer',
        'cost_credits' => 'integer',
        'provider_response' => 'array',
        'meta' => 'array',
        'queued_at' => 'datetime',
        'sent_at' => 'datetime',
    ];
}
