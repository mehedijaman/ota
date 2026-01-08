<?php

namespace Modules\Menu\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Menu\Database\Factories\MenuFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;

class Menu extends BaseModel
{
    use ActivityLog, HasFactory, Searchable, SoftDeletes;

    protected $table = 'menus';

    protected $casts = [
        'active' => 'boolean',
    ];

    protected static function newFactory(): Factory
    {
        return MenuFactory::new();
    }

    public function items()
    {
        return $this->hasMany(MenuItem::class);
    }
}
