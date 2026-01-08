<?php

use Illuminate\Support\Facades\Route;
use Modules\Menu\Http\Controllers\MenuController;
use Modules\Menu\Http\Controllers\MenuItemController;

/** menu Routes */
Route::prefix('menu')->name('menu.')->controller(MenuController::class)->group(function () {
    Route::get('/', 'index')
        ->name('index')
        ->can('menu-list');

    Route::get('/create', 'create')
        ->name('create')
        ->can('menu-create');

    Route::post('/', 'store')
        ->name('store')
        ->can('menu-create');

    Route::get('/{id}/edit', 'edit')
        ->name('edit')
        ->can('menu-edit');

    Route::put('/{id}', 'update')
        ->name('update')
        ->can('menu-edit');

    Route::delete('/{id}', 'destroy')
        ->name('destroy')
        ->can('menu-delete');

    /** Recycle Bin Routes */
    Route::prefix('recycle-bin')->name('recycleBin.')->group(function () {
        Route::get('/', 'recycleBin')
            ->name('index')
            ->can('menu-recycle-bin-list');

        Route::get('/{id}/restore', 'restore')
            ->name('restore')
            ->can('menu-recycle-Bin-Restore');

        Route::delete('/{id}/destroy', 'destroyForce')
            ->name('destroyForce')
            ->can('menu-recycle-bin-delete');

        Route::delete('/empty', 'emptyRecycleBin')
            ->name('empty')
            ->can('menu-recycle-bin-delete');

        Route::get('/restore', 'restoreRecycleBin')
            ->name('restoreAll')
            ->can('menu-recycle-Bin-Restore');
    });
});

/** Menu Item Routes */
Route::prefix('menu/{menu}')->name('menu.')->group(function () {
    Route::get('/items', [MenuItemController::class, 'index'])->name('items.index');
    Route::post('/items', [MenuItemController::class, 'store'])->name('items.store');
    Route::post('/items/order', [MenuItemController::class, 'updateOrder'])->name('items.updateOrder');
    Route::put('/items/{menuItem}', [MenuItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{menuItem}', [MenuItemController::class, 'destroy'])->name('items.destroy');
});
