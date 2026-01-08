<?php

namespace Modules\Menu\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Modules\Menu\Models\Menu;
use Modules\Menu\Models\MenuItem;
use Modules\Page\Models\Page;
use Modules\Support\Http\Controllers\BackendController;

class MenuItemController extends BackendController
{
    public function index(Menu $menu): Response
    {
        $menuItems = $menu->items()->with('children')->whereNull('parent_id')->orderBy('order')->get();

        // Get available pages for the menu builder
        $availablePages = Page::where('active', true)
            ->select('id', 'title', 'slug')
            ->orderBy('title')
            ->get();

        return inertia('Menu/MenuItemIndex', [
            'menu' => $menu,
            'menuItems' => $menuItems,
            'availablePages' => $availablePages,
        ]);
    }

    public function updateOrder(Menu $menu, Request $request): JsonResponse
    {
        foreach ($request->input('items') as $itemData) {
            $item = MenuItem::find($itemData['id']);

            if ($item) {
                $item->order = $itemData['order'];
                $item->parent_id = $itemData['parent_id'];
                $item->url = $itemData['url'];
                $item->target = $itemData['target'];
                $item->save();
            }
        }

        $menuItems = $menu->items()->with('children')->whereNull('parent_id')->orderBy('order')->get();

        return response()->json([
            'message' => 'Order updated successfully',
            'menuItems' => $menuItems,
        ]);
    }

    public function store(Menu $menu, Request $request): JsonResponse
    {
        $menuItem = new MenuItem;
        $menuItem->menu_id = $menu->id;
        $menuItem->title = $request->input('title');
        $menuItem->url = $request->input('url');
        $menuItem->target = $request->input('target');
        $menuItem->parent_id = $request->input('parent_id');
        $menuItem->save();

        $menuItems = $menu->items()->with('children')->whereNull('parent_id')->orderBy('order')->get();

        return response()->json([
            'message' => 'Menu item added successfully',
            'menuItems' => $menuItems,
        ]);
    }

    public function update(Menu $menu, MenuItem $menuItem, Request $request): JsonResponse
    {
        $menuItem->title = $request->input('title');
        $menuItem->url = $request->input('url');
        $menuItem->target = $request->input('target');
        $menuItem->save();

        $menuItems = $menu->items()->with('children')->whereNull('parent_id')->orderBy('order')->get();

        return response()->json([
            'message' => 'Menu item updated successfully',
            'menuItems' => $menuItems,
        ]);
    }

    public function destroy(Menu $menu, MenuItem $menuItem): JsonResponse
    {
        $menuItem->delete();
        $menuItems = $menu->items()->with('children')->whereNull('parent_id')->orderBy('order')->get();

        return response()->json([
            'message' => 'Menu item deleted successfully',
            'menuItems' => $menuItems,
        ]);
    }
}
