<?php

namespace Modules\Faq;

use Modules\Support\BaseServiceProvider;

class FaqServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');

        $this->loadViewsFrom(__DIR__.'/views', 'faq');
    }
}
