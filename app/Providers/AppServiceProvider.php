<?php

namespace App\Providers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Modules\Menu\Models\Menu;
use Modules\Setting\Models\Setting;

class AppServiceProvider extends ServiceProvider
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
        $this->setSiteSettings();
        $this->setMenus(['primary', 'footer']);
        $this->shareGlobals();
    }

    /**
     * Load and cache site settings.
     */
    protected function setSiteSettings(): void
    {
        if (! Session::has('settings')) {
            $settings = Setting::firstOrCreate(); // Adjust logic if needed
            Session::put('settings', $settings);
        }
    }

    /**
     * Load and cache menus based on positions.
     */
    protected function setMenus(array $positions): void
    {
        $menus = Menu::with(['items' => function ($query) {
            $query->whereNull('parent_id')->orderBy('order');
        }])->whereIn('position', $positions)->get();

        foreach ($positions as $position) {
            $menuItems = $menus->firstWhere('position', $position)?->items;
            if ($menuItems) {
                Session::put("{$position}Menu", $menuItems);
            }
        }
    }

    /**
     * Share global variables with Blade views.
     */
    protected function shareGlobals(): void
    {
        View::share('siteSettings', Session::get('settings'));
        View::share('primaryMenu', Session::get('primaryMenu'));
        View::share('footerMenu', Session::get('footerMenu'));
    }
}
