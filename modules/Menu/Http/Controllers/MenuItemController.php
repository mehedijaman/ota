<?php

namespace Modules\Menu\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Response;
use Modules\Menu\Models\Menu;
use Modules\Menu\Models\MenuItem;
use Modules\Support\Http\Controllers\BackendController;

class MenuItemController extends BackendController
{
    public function index(Menu $menu): Response
    {
        $menuItems = $menu->items()->with('children')->whereNull('parent_id')->orderBy('order')->get();

        return inertia('Menu/MenuItemIndex', [
            'menu' => $menu,
            'menuItems' => $menuItems,
        ]);
    }

    public function updateOrder(Menu $menu, Request $request)
    {
        foreach ($request->input('items') as $itemData) {
            $item = MenuItem::find($itemData['id']);

            if ($item) {
                $item->order = $itemData['order'];
                $item->parent_id = $itemData['parent_id'];
                $item->url = $itemData['url']; // Added url and target from vue changes.
                $item->target = $itemData['target'];
                $item->save();
            } else {
                \Log::error("Menu item with ID {$itemData['id']} not found.");
            }
        }

        // return response()->json(['message' => 'Order updated successfully']);
    }

    public function store(Menu $menu, Request $request): Response
    {
        $menuItem = new MenuItem;
        $menuItem->menu_id = $menu->id;
        $menuItem->title = $request->input('title');
        $menuItem->url = $request->input('url');
        $menuItem->target = $request->input('target');
        $menuItem->parent_id = $request->input('parent_id');
        $menuItem->save();

        $menuItems = $menu->items()->with('children')->whereNull('parent_id')->orderBy('order')->get();

        return inertia('Menu/MenuItemIndex', [
            'menu' => $menu,
            'menuItems' => $menuItems,
        ]);
    }

    public function destroy(Menu $menu, MenuItem $menuItem): Response
    {
        $menuItem->delete();
        $menuItems = $menu->items()->with('children')->whereNull('parent_id')->orderBy('order')->get();

        return inertia('Menu/MenuItemIndex', [
            'menu' => $menu,
            'menuItems' => $menuItems,
        ]);
    }
}
