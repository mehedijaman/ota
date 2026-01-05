<?php

namespace Modules\Agent;

use Modules\Support\BaseServiceProvider;

class AgentServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
    }
}
