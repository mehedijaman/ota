<?php

namespace Modules\Bill;

use Modules\Support\BaseServiceProvider;

class BillServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');

        $this->loadViewsFrom(__DIR__.'/views', 'bill');
    }
}
