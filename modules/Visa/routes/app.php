<?php

use Illuminate\Support\Facades\Route;
use Modules\Visa\Http\Controllers\VisaApplicationController;
use Modules\Visa\Http\Controllers\VisaCategoryController;
use Modules\Visa\Http\Controllers\VisaController;
use Modules\Visa\Http\Controllers\VisaCountryController;

Route::middleware(['auth', 'verified'])->group(function () {

    // Visa Countries
    Route::prefix('visa-countries')->controller(VisaCountryController::class)->name('visaCountry.')->group(function () {
        // Recycle Bin
        Route::get('/recycle-bin', 'recycleBin')->name('recycleBin')->can('visa-country-delete');
        Route::post('/recycle-bin/{id}/restore', 'restore')->name('restore')->can('visa-country-delete');
        Route::delete('/recycle-bin/{id}/force', 'destroyForce')->name('destroyForce')->can('visa-country-delete');
        Route::delete('/recycle-bin/empty', 'emptyRecycleBin')->name('emptyRecycleBin')->can('visa-country-delete');
        Route::post('/recycle-bin/restore-all', 'restoreRecycleBin')->name('restoreRecycleBin')->can('visa-country-delete');

        // Standard CRUD
        Route::get('/', 'index')->name('index')->can('visa-country-list');
        Route::get('create', 'create')->name('create')->can('visa-country-create');
        Route::post('/', 'store')->name('store')->can('visa-country-create');
        Route::get('{id}', 'show')->name('show')->can('visa-country-view');
        Route::get('{id}/edit', 'edit')->name('edit')->can('visa-country-edit');
        Route::put('{id}', 'update')->name('update')->can('visa-country-edit');
        Route::delete('{id}', 'destroy')->name('destroy')->can('visa-country-delete');
    });

    // Visa Categories
    Route::prefix('visa-categories')->controller(VisaCategoryController::class)->name('visaCategory.')->group(function () {
        // Recycle Bin
        Route::get('/recycle-bin', 'recycleBin')->name('recycleBin')->can('visa-category-delete');
        Route::post('/recycle-bin/{id}/restore', 'restore')->name('restore')->can('visa-category-delete');
        Route::delete('/recycle-bin/{id}/force', 'destroyForce')->name('destroyForce')->can('visa-category-delete');
        Route::delete('/recycle-bin/empty', 'emptyRecycleBin')->name('emptyRecycleBin')->can('visa-category-delete');
        Route::post('/recycle-bin/restore-all', 'restoreRecycleBin')->name('restoreRecycleBin')->can('visa-category-delete');

        // Standard CRUD
        Route::get('/', 'index')->name('index')->can('visa-category-list');
        Route::get('create', 'create')->name('create')->can('visa-category-create');
        Route::post('/', 'store')->name('store')->can('visa-category-create');
        Route::get('{id}/edit', 'edit')->name('edit')->can('visa-category-edit');
        Route::put('{id}', 'update')->name('update')->can('visa-category-edit');
        Route::delete('{id}', 'destroy')->name('destroy')->can('visa-category-delete');
    });

    // Visas
    Route::prefix('visas')->controller(VisaController::class)->name('visa.')->group(function () {
        // Recycle Bin
        Route::get('/recycle-bin', 'recycleBin')->name('recycleBin')->can('visa-delete');
        Route::post('/recycle-bin/{id}/restore', 'restore')->name('restore')->can('visa-delete');
        Route::delete('/recycle-bin/{id}/force', 'destroyForce')->name('destroyForce')->can('visa-delete');
        Route::delete('/recycle-bin/empty', 'emptyRecycleBin')->name('emptyRecycleBin')->can('visa-delete');
        Route::post('/recycle-bin/restore-all', 'restoreRecycleBin')->name('restoreRecycleBin')->can('visa-delete');

        // Standard CRUD
        Route::get('/', 'index')->name('index')->can('visa-list');
        Route::get('create', 'create')->name('create')->can('visa-create');
        Route::post('/', 'store')->name('store')->can('visa-create');
        Route::get('{id}', 'show')->name('show')->can('visa-view');
        Route::get('{id}/edit', 'edit')->name('edit')->can('visa-edit');
        Route::put('{id}', 'update')->name('update')->can('visa-edit');
        Route::delete('{id}', 'destroy')->name('destroy')->can('visa-delete');
    });

    // Visa Applications
    Route::prefix('visa-applications')->controller(VisaApplicationController::class)->name('visaApplication.')->group(function () {
        // Recycle Bin
        Route::get('/recycle-bin', 'recycleBin')->name('recycleBin')->can('visa-application-delete');
        Route::post('/recycle-bin/{id}/restore', 'restore')->name('restore')->can('visa-application-delete');
        Route::delete('/recycle-bin/{id}/force', 'destroyForce')->name('destroyForce')->can('visa-application-delete');
        Route::delete('/recycle-bin/empty', 'emptyRecycleBin')->name('emptyRecycleBin')->can('visa-application-delete');
        Route::post('/recycle-bin/restore-all', 'restoreRecycleBin')->name('restoreRecycleBin')->can('visa-application-delete');

        // Standard CRUD
        Route::get('/', 'index')->name('index')->can('visa-application-list');
        Route::get('create', 'create')->name('create')->can('visa-application-create');
        Route::post('/', 'store')->name('store')->can('visa-application-create');
        Route::get('{id}', 'show')->name('show')->can('visa-application-view');
        Route::get('{id}/edit', 'edit')->name('edit')->can('visa-application-edit');
        Route::put('{id}', 'update')->name('update')->can('visa-application-edit');
        Route::patch('{id}/status/{status}', 'updateStatus')->name('updateStatus')->can('visa-application-edit');
        Route::post('{id}/payment', 'addPayment')->name('addPayment')->can('visa-application-edit');
        Route::delete('{id}', 'destroy')->name('destroy')->can('visa-application-delete');
    });
});
