<?php

namespace Modules\Visa;

use Modules\Support\BaseServiceProvider;

class VisaServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');

        $this->loadViewsFrom(__DIR__.'/views', 'visa');
    }
}
