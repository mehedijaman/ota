<?php

namespace Modules\Message;

use Modules\Support\BaseServiceProvider;

class MessageServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'message');
    }
}
