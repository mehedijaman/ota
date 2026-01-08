<?php

namespace Modules\Menu\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;

class Menu extends BaseModel
{
    use ActivityLog, Searchable, SoftDeletes;

    protected $table = 'menus';

    protected $casts = [
        'active' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(MenuItem::class);
    }
}
