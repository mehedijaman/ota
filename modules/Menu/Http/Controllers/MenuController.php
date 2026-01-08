<?php

namespace Modules\Menu\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Menu\Http\Requests\MenuValidate;
use Modules\Menu\Models\Menu;
use Modules\Menu\Services\MenuPositionOptions;
use Modules\Support\Http\Controllers\BackendController;

class MenuController extends BackendController
{
    public function index(): Response
    {
        $menus = Menu::orderBy('name')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($menu) => [
                'id' => $menu->id,
                'name' => $menu->name,
                'position' => $menu->position,
                'active' => $menu->active,
            ]);

        return inertia('Menu/MenuIndex', [
            'menus' => $menus,
        ]);
    }

    public function create(): Response
    {
        $positions = MenuPositionOptions::get();

        return inertia('Menu/MenuForm', [
            'positions' => $positions,
        ]);
    }

    public function store(MenuValidate $request): RedirectResponse
    {
        Menu::create($request->validated());

        return redirect()->route('menu.index')
            ->with('success', 'menu created.');
    }

    public function edit(int $id): Response
    {
        $menu = Menu::find($id);
        $positions = MenuPositionOptions::get();

        return inertia('Menu/MenuForm', [
            'menu' => $menu,
            'positions' => $positions,
        ]);
    }

    public function update(MenuValidate $request, int $id): RedirectResponse
    {
        $menu = Menu::findOrFail($id);

        $menu->update($request->validated());

        return redirect()->route('menu.index')
            ->with('success', 'menuupdated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Menu::findOrFail($id)->delete();

        return redirect()->route('menu.index')
            ->with('success', 'menudeleted.');
    }

    public function recycleBin(): Response
    {
        $menus = Menu::onlyTrashed()
            ->orderBy('id')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($menu) => [
                'id' => $menu->id,
                'name' => $menu->name,
                'deleted_at' => $menu->deleted_at ? Carbon::parse($menu->deleted_at)->format('d/m/Y') : null,
                'deletedBy' => $menu->deletedBy,
                'active' => $menu->active,
            ]);

        return inertia('Menu/MenuRecycleBin', [
            'menus' => $menus,
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        Menu::onlyTrashed()->findOrFail($id)->restore(); // Restore soft deleted record

        return redirect()->route('menu.recycleBin.index')
            ->with('success', 'menuRestored.');
    }

    public function destroyForce(int $id): RedirectResponse
    {

        $menu = Menu::onlyTrashed()->findOrFail($id);

        $menu->forceDelete();

        return redirect()->route('menu.recycleBin.index')->with('success', 'menu deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $menus = Menu::onlyTrashed()->get();

        foreach ($menus as $menu) {
            $menu->forceDelete();
        }

        return redirect()->route('menu.recycleBin.index')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        Menu::onlyTrashed()->restore(); // Restore soft deleted records

        return redirect()->route('menu.recycleBin.index')
            ->with('success', 'menu Restored.');
    }
}
