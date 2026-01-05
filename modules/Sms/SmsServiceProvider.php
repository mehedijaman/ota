<?php

namespace Modules\Sms;

use Modules\Support\BaseServiceProvider;

class SmsServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'sms');
    }
}
