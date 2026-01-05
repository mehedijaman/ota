<?php

namespace Modules\Sms\Models;

use Modules\Support\Models\BaseModel;

class SmsTenantSetting extends BaseModel
{
    protected $table = 'sms_tenant_settings';

    protected $fillable = [
        'tenant_id',
        'sale_sms_enabled',
        'sale_sms_template',
        'invoice_sms_enabled',
        'invoice_sms_template',
        'due_reminder_sms_enabled',
        'due_reminder_template',
    ];

    protected $casts = [
        'sale_sms_enabled' => 'boolean',
        'invoice_sms_enabled' => 'boolean',
        'due_reminder_sms_enabled' => 'boolean',
    ];
}
