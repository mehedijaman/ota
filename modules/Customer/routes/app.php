<?php

use Illuminate\Support\Facades\Route;
use Modules\Customer\Http\Controllers\CustomerController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('customers')->controller(CustomerController::class)->name('customer.')->group(function () {

        // Recycle Bin
        Route::get('/recycle-bin', 'recycleBin')->name('recycleBin')->can('customer-delete');
        Route::post('/recycle-bin/{id}/restore', 'restore')->name('restore')->can('customer-delete');
        Route::delete('/recycle-bin/{id}/force', 'destroyForce')->name('destroyForce')->can('customer-delete');
        Route::delete('/recycle-bin/empty', 'emptyRecycleBin')->name('emptyRecycleBin')->can('customer-delete');
        Route::post('/recycle-bin/restore-all', 'restoreRecycleBin')->name('restoreRecycleBin')->can('customer-delete');

        // Standard CRUD
        Route::get('/', 'index')->name('index')->can('customer-list');
        Route::get('create', 'create')->name('create')->can('customer-create');
        Route::get('{customer:uuid}', 'show')->name('show')->can('customer-view');
        Route::post('/', 'store')->name('store')->can('customer-create');
        Route::get('{customer:uuid}/edit', 'edit')->name('edit')->can('customer-edit');
        Route::put('{customer:uuid}', 'update')->name('update')->can('customer-edit');
        Route::delete('{customer:uuid}', 'destroy')->name('destroy')->can('customer-delete');
        Route::post('{customer:uuid}/create-user', 'createUser')->name('createUser')->can('customer-edit');
    });
});
