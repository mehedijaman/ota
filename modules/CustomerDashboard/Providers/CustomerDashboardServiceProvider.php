<?php

namespace Modules\CustomerDashboard\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CustomerDashboardServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadRoutes();
    }

    /**
     * Load module routes.
     */
    protected function loadRoutes(): void
    {
        Route::middleware(['web'])
            ->group(__DIR__.'/../routes/app.php');
    }
}
