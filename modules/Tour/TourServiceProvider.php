<?php

namespace Modules\Tour;

use Modules\Support\BaseServiceProvider;

class TourServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');

        $this->loadViewsFrom(__DIR__.'/views', 'tour');
    }
}
