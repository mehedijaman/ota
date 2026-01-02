<?php

namespace Modules\Hotel;

use Modules\Support\BaseServiceProvider;

class HotelServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');

        $this->loadViewsFrom(__DIR__.'/views', 'hotel');
    }
}
