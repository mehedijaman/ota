<?php

namespace Modules\Setting\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;

class AISetting extends BaseModel
{
    use ActivityLog, Searchable, SoftDeletes;

    protected $table = 'AISettings';

    public static function getDefault()
    {
        return self::where('is_default', true)->first();
    }
}
