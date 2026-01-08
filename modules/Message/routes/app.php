<?php

use Illuminate\Support\Facades\Route;
use Modules\Message\Http\Controllers\MessageController;

/** message Routes */
Route::prefix('message')->name('message.')->controller(MessageController::class)->group(function () {
    Route::get('/', 'index')
        ->name('index')
        ->can('message-list');

    Route::get('/create', 'create')
        ->name('create')
        ->can('message-create');

    Route::post('/', 'store')
        ->name('store')
        ->can('message-create');

    Route::get('/{id}/show', 'show')
        ->name('show')
        ->can('message-show');

    Route::put('/{id}', 'update')
        ->name('update')
        ->can('message-edit');

    Route::delete('/{id}', 'destroy')
        ->name('destroy')
        ->can('message-delete');

    /** Recycle Bin Routes */
    Route::prefix('recycle-bin')->name('recycleBin.')->group(function () {
        Route::get('/', 'recycleBin')
            ->name('index')
            ->can('message-recycle-bin-list');

        Route::get('/{id}/restore', 'restore')
            ->name('restore')
            ->can('message-recycle-Bin-Restore');

        Route::delete('/{id}/destroy', 'destroyForce')
            ->name('destroyForce')
            ->can('message-recycle-bin-delete');

        Route::delete('/empty', 'emptyRecycleBin')
            ->name('empty')
            ->can('message-recycle-bin-delete');

        Route::get('/restore', 'restoreRecycleBin')
            ->name('restoreAll')
            ->can('message-recycle-Bin-Restore');
    });
});
