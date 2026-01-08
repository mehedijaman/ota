<?php

namespace Modules\Menu\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Menu\Database\Factories\MenuItemFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;

class MenuItem extends BaseModel
{
    use ActivityLog, HasFactory, Searchable;

    protected $table = 'menu_items';

    protected static function booted(): void
    {
        static::saved(fn () => Menu::clearMenuCache());
        static::deleted(fn () => Menu::clearMenuCache());
    }

    protected static function newFactory(): Factory
    {
        return MenuItemFactory::new();
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('order');
    }
}
