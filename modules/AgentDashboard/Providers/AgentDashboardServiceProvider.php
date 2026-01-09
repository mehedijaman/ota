<?php

namespace Modules\AgentDashboard\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AgentDashboardServiceProvider extends ServiceProvider
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
