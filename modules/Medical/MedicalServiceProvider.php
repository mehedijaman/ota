<?php

namespace Modules\Medical;

use Modules\Support\BaseServiceProvider;

class MedicalServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');

        $this->loadViewsFrom(__DIR__.'/views', 'medical');
    }
}
