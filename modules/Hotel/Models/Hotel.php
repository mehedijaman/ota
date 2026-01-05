<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;

class Hotel extends BaseModel
{
    use ActivityLog, Searchable, SoftDeletes;

    protected $table = 'hotels';
}
