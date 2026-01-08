<?php

namespace Modules\Setting;

use Modules\Support\BaseServiceProvider;

class SettingServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'setting');
    }
}
