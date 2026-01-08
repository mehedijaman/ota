<?php

namespace Modules\Message\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Message\Database\Factories\MessageFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;

class Message extends BaseModel
{
    use ActivityLog, HasFactory, Searchable, SoftDeletes;

    protected $table = 'messages';

    protected static function newFactory(): Factory
    {
        return MessageFactory::new();
    }
}
