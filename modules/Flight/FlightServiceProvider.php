<?php

namespace Modules\Flight;

use Modules\Support\BaseServiceProvider;

class FlightServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'flight');
    }
}
