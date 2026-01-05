<?php

namespace Modules\Sms\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Support\Models\BaseModel;

class SmsCreditReservation extends BaseModel
{
    protected $table = 'sms_credit_reservations';

    protected $fillable = [
        'tenant_id',
        'sms_credit_lot_id',
        'sms_message_id',
        'credits',
    ];

    protected $casts = [
        'credits' => 'integer',
    ];

    public function lot(): BelongsTo
    {
        return $this->belongsTo(SmsCreditLot::class, 'sms_credit_lot_id');
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(SmsMessage::class, 'sms_message_id');
    }
}
