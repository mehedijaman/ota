<?php

namespace Modules\Menu;

use Modules\Support\BaseServiceProvider;

class MenuServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'menu');
    }
}
