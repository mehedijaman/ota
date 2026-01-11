<?php

use Illuminate\Support\Facades\Route;
use Modules\Tour\Http\Controllers\TourBookingController;
use Modules\Tour\Http\Controllers\TourCategoryController;
use Modules\Tour\Http\Controllers\TourController;
use Modules\Tour\Http\Controllers\TourDestinationController;
use Modules\Tour\Http\Controllers\TourReviewController;

Route::middleware(['auth', 'verified'])->group(function () {

    // Tour Categories
    Route::prefix('tour-categories')->controller(TourCategoryController::class)->name('tourCategory.')->group(function () {
        // Recycle Bin
        Route::get('/recycle-bin', 'recycleBin')->name('recycleBin')->can('tour-category-delete');
        Route::post('/recycle-bin/{id}/restore', 'restore')->name('restore')->can('tour-category-delete');
        Route::delete('/recycle-bin/{id}/force', 'destroyForce')->name('destroyForce')->can('tour-category-delete');
        Route::delete('/recycle-bin/empty', 'emptyRecycleBin')->name('emptyRecycleBin')->can('tour-category-delete');
        Route::post('/recycle-bin/restore-all', 'restoreRecycleBin')->name('restoreRecycleBin')->can('tour-category-delete');

        // Standard CRUD
        Route::get('/', 'index')->name('index')->can('tour-category-list');
        Route::get('create', 'create')->name('create')->can('tour-category-create');
        Route::post('/', 'store')->name('store')->can('tour-category-create');
        Route::get('{id}/edit', 'edit')->name('edit')->can('tour-category-edit');
        Route::put('{id}', 'update')->name('update')->can('tour-category-edit');
        Route::delete('{id}', 'destroy')->name('destroy')->can('tour-category-delete');
    });

    // Tour Destinations
    Route::prefix('tour-destinations')->controller(TourDestinationController::class)->name('tourDestination.')->group(function () {
        // Recycle Bin
        Route::get('/recycle-bin', 'recycleBin')->name('recycleBin')->can('tour-destination-delete');
        Route::post('/recycle-bin/{id}/restore', 'restore')->name('restore')->can('tour-destination-delete');
        Route::delete('/recycle-bin/{id}/force', 'destroyForce')->name('destroyForce')->can('tour-destination-delete');
        Route::delete('/recycle-bin/empty', 'emptyRecycleBin')->name('emptyRecycleBin')->can('tour-destination-delete');
        Route::post('/recycle-bin/restore-all', 'restoreRecycleBin')->name('restoreRecycleBin')->can('tour-destination-delete');

        // Standard CRUD
        Route::get('/', 'index')->name('index')->can('tour-destination-list');
        Route::get('create', 'create')->name('create')->can('tour-destination-create');
        Route::post('/', 'store')->name('store')->can('tour-destination-create');
        Route::get('{id}/edit', 'edit')->name('edit')->can('tour-destination-edit');
        Route::put('{id}', 'update')->name('update')->can('tour-destination-edit');
        Route::delete('{id}', 'destroy')->name('destroy')->can('tour-destination-delete');
    });

    // Tours
    Route::prefix('tours')->controller(TourController::class)->name('tour.')->group(function () {
        // Recycle Bin
        Route::get('/recycle-bin', 'recycleBin')->name('recycleBin')->can('tour-delete');
        Route::post('/recycle-bin/{id}/restore', 'restore')->name('restore')->can('tour-delete');
        Route::delete('/recycle-bin/{id}/force', 'destroyForce')->name('destroyForce')->can('tour-delete');
        Route::delete('/recycle-bin/empty', 'emptyRecycleBin')->name('emptyRecycleBin')->can('tour-delete');
        Route::post('/recycle-bin/restore-all', 'restoreRecycleBin')->name('restoreRecycleBin')->can('tour-delete');

        // Standard CRUD
        Route::get('/', 'index')->name('index')->can('tour-list');
        Route::get('create', 'create')->name('create')->can('tour-create');
        Route::post('/', 'store')->name('store')->can('tour-create');
        Route::get('{id}', 'show')->name('show')->can('tour-view');
        Route::get('{id}/edit', 'edit')->name('edit')->can('tour-edit');
        Route::put('{id}', 'update')->name('update')->can('tour-edit');
        Route::delete('{id}', 'destroy')->name('destroy')->can('tour-delete');
    });

    // Tour Bookings
    Route::prefix('tour-bookings')->controller(TourBookingController::class)->name('tourBooking.')->group(function () {
        Route::get('/', 'index')->name('index')->can('tour-booking-list');
        Route::get('create', 'create')->name('create')->can('tour-booking-create');
        Route::post('/', 'store')->name('store')->can('tour-booking-create');
        Route::get('{id}', 'show')->name('show')->can('tour-booking-view');
        Route::get('{id}/edit', 'edit')->name('edit')->can('tour-booking-edit');
        Route::put('{id}', 'update')->name('update')->can('tour-booking-edit');
        Route::patch('{id}/status/{status}', 'updateStatus')->name('updateStatus')->can('tour-booking-edit');
        Route::delete('{id}', 'destroy')->name('destroy')->can('tour-booking-delete');
    });

    // Tour Reviews
    Route::prefix('tour-reviews')->controller(TourReviewController::class)->name('tourReview.')->group(function () {
        Route::get('/', 'index')->name('index')->can('tour-review-list');
        Route::get('{id}', 'show')->name('show')->can('tour-review-view');
        Route::patch('{id}/approve', 'approve')->name('approve')->can('tour-review-edit');
        Route::patch('{id}/reject', 'reject')->name('reject')->can('tour-review-edit');
        Route::patch('{id}/toggle-featured', 'toggleFeatured')->name('toggleFeatured')->can('tour-review-edit');
        Route::delete('{id}', 'destroy')->name('destroy')->can('tour-review-delete');
    });

});
