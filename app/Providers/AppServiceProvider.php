<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Modules\Menu\Models\Menu;
use Modules\Setting\Services\SettingService;
use Raziul\Sslcommerz\SslcommerzClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SettingService::class);

        // Override SSLCommerz singleton with a simpler binding that doesn't require routes at bind time
        $this->app->singleton(SslcommerzClient::class, function ($app) {
            $config = $app->config->get('sslcommerz');

            if (empty($config['store']['id']) || empty($config['store']['password'])) {
                throw new \Raziul\Sslcommerz\Exceptions\SslcommerzException('SSLCommerz store credentials are not set.');
            }

            return new SslcommerzClient(
                $config['store']['id'],
                $config['store']['password'],
                $config['store']['currency'] ?? 'BDT',
                $config['sandbox'] ?? true
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->shareGlobals();
    }

    /**
     * Share global variables with Blade views.
     */
    protected function shareGlobals(): void
    {
        View::composer('*', function ($view) {
            // Cache site settings for 1 hour
            $siteSettings = Cache::remember('settings', 3600, function () {
                return app(SettingService::class)->getPublicSettingsObject();
            });

            // Cache menus for 1 hour
            $menus = Cache::remember('menu', 3600, function () {
                return [
                    'primary' => $this->getMenuItems('primary'),
                    'footer' => $this->getMenuItems('footer'),
                ];
            });

            $view->with('siteSettings', $siteSettings);
            $view->with('primaryMenu', $menus['primary']);
            $view->with('footerMenu', $menus['footer']);
        });
    }

    /**
     * Get menu items by position.
     */
    protected function getMenuItems(string $position)
    {
        $menu = Menu::with(['items' => function ($query) {
            $query->whereNull('parent_id')
                ->with(['children' => function ($q) {
                    $q->orderBy('order');
                }])
                ->orderBy('order');
        }])->where('position', $position)->first();

        return $menu?->items ?? collect();
    }
}
