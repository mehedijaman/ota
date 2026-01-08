<?php

namespace Modules\Menu\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Menu\Models\Menu;
use Modules\Menu\Models\MenuItem;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPrimaryMenu();
        $this->seedFooterMenu();
    }

    protected function seedPrimaryMenu(): void
    {
        $menu = Menu::factory()->primary()->create();

        $menuItems = [
            ['title' => 'Home', 'url' => '/', 'order' => 1],
            ['title' => 'Flight', 'url' => '/flight', 'order' => 2],
            ['title' => 'Hotel', 'url' => '/hotel', 'order' => 3],
            ['title' => 'Tour', 'url' => '/tour', 'order' => 4],
            ['title' => 'Visa', 'url' => '/visa', 'order' => 5],
        ];

        foreach ($menuItems as $item) {
            MenuItem::factory()->forMenu($menu)->create([
                'title' => $item['title'],
                'url' => $item['url'],
                'order' => $item['order'],
            ]);
        }

        $moreDropdown = MenuItem::factory()->forMenu($menu)->create([
            'title' => 'More',
            'url' => '#',
            'order' => 6,
        ]);

        $moreChildren = [
            ['title' => 'Bill', 'url' => '/bill', 'order' => 1],
            ['title' => 'Medical', 'url' => '/medical', 'order' => 2],
            ['title' => 'Transport', 'url' => 'https://12go.asia/en', 'target' => '_blank', 'order' => 3],
            ['title' => 'FAQ', 'url' => '/faq', 'order' => 4],
            ['title' => 'About', 'url' => '/about', 'order' => 5],
            ['title' => 'Why Us', 'url' => '/why-us', 'order' => 6],
            ['title' => 'Blogs', 'url' => '/blog', 'order' => 7],
        ];

        foreach ($moreChildren as $child) {
            MenuItem::factory()->withParent($moreDropdown)->create([
                'title' => $child['title'],
                'url' => $child['url'],
                'target' => $child['target'] ?? '_self',
                'order' => $child['order'],
            ]);
        }
    }

    protected function seedFooterMenu(): void
    {
        $menu = Menu::factory()->footer()->create();

        $footerItems = [
            ['title' => 'Home', 'url' => '/', 'order' => 1],
            ['title' => 'About Us', 'url' => '/about', 'order' => 2],
            ['title' => 'Privacy Policy', 'url' => '/privacy-policy', 'order' => 3],
            ['title' => 'Terms & Conditions', 'url' => '/terms-and-conditions', 'order' => 4],
            ['title' => 'FAQ', 'url' => '/faq', 'order' => 5],
            ['title' => 'Blog', 'url' => '/blog', 'order' => 6],
        ];

        foreach ($footerItems as $item) {
            MenuItem::factory()->forMenu($menu)->create([
                'title' => $item['title'],
                'url' => $item['url'],
                'order' => $item['order'],
            ]);
        }
    }
}
