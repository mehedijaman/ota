<?php

namespace Modules\Sms\Models;

use Modules\Support\Models\BaseModel;

class SmsCampaignRecipient extends BaseModel
{
    protected $table = 'sms_campaign_recipients';

    protected $fillable = [
        'campaign_id',
        'phone',
        'status',
        'provider_response',
        'error_message',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'provider_response' => 'array',
            'sent_at' => 'datetime',
        ];
    }

    public function campaign()
    {
        return $this->belongsTo(SmsCampaign::class, 'campaign_id');
    }
}
