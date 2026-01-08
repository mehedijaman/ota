<?php

use Illuminate\Support\Facades\Route;
use Modules\Testimonial\Http\Controllers\TestimonialController;

/** testimonial Routes */
Route::prefix('testimonial')->name('testimonial.')->controller(TestimonialController::class)->group(function () {
    Route::get('/', 'index')
        ->name('index')
        ->can('testimonial-list');

    Route::get('/create', 'create')
        ->name('create')
        ->can('testimonial-create');

    Route::post('/', 'store')
        ->name('store')
        ->can('testimonial-create');

    Route::get('/{id}/edit', 'edit')
        ->name('edit')
        ->can('testimonial-edit');

    Route::put('/{id}', 'update')
        ->name('update')
        ->can('testimonial-edit');

    Route::delete('/{id}', 'destroy')
        ->name('destroy')
        ->can('testimonial-delete');

    /** Recycle Bin Routes */
    Route::prefix('recycle-bin')->name('recycleBin.')->group(function () {
        Route::get('/', 'recycleBin')
            ->name('index')
            ->can('testimonial-recycle-bin-list');

        Route::get('/{id}/restore', 'restore')
            ->name('restore')
            ->can('testimonial-recycle-Bin-Restore');

        Route::delete('/{id}/destroy', 'destroyForce')
            ->name('destroyForce')
            ->can('testimonial-recycle-bin-delete');

        Route::delete('/empty', 'emptyRecycleBin')
            ->name('empty')
            ->can('testimonial-recycle-bin-delete');

        Route::get('/restore', 'restoreRecycleBin')
            ->name('restoreAll')
            ->can('testimonial-recycle-Bin-Restore');
    });
});
