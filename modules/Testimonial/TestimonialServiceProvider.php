<?php

namespace Modules\Testimonial;

use Modules\Support\BaseServiceProvider;

class TestimonialServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'testimonial');
    }
}
