<?php

namespace Modules\Sms\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Modules\Support\Models\BaseModel;

class SmsOtp extends BaseModel
{
    protected $table = 'sms_otps';

    protected $fillable = [
        'purpose',
        'phone',
        'code_hash',
        'attempts',
        'expires_at',
        'consumed_at',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'consumed_at' => 'datetime',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->whereNull('consumed_at')
            ->where('expires_at', '>', Carbon::now());
    }
}
