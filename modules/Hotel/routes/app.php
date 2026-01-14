<?php

use Illuminate\Support\Facades\Route;
use Modules\Hotel\Http\Controllers\HotelBookingController;
use Modules\Hotel\Http\Controllers\HotelController;
use Modules\Hotel\Http\Controllers\HotelReviewController;
use Modules\Hotel\Http\Controllers\HotelRoomController;

Route::middleware(['auth', 'verified'])->group(function () {

    // Hotels
    Route::prefix('hotels')->controller(HotelController::class)->name('hotel.')->group(function () {
        // Recycle Bin
        Route::get('/recycle-bin', 'recycleBin')->name('recycleBin')->can('hotel.view');
        Route::post('/recycle-bin/{id}/restore', 'restore')->name('restore')->can('hotel.restore');
        Route::delete('/recycle-bin/{id}/force', 'destroyForce')->name('destroyForce')->can('hotel.delete');
        Route::delete('/recycle-bin/empty', 'emptyRecycleBin')->name('emptyRecycleBin')->can('hotel.delete');
        Route::post('/recycle-bin/restore-all', 'restoreRecycleBin')->name('restoreRecycleBin')->can('hotel.restore');

        // Standard CRUD
        Route::get('/', 'index')->name('index')->can('hotel.view');
        Route::get('create', 'create')->name('create')->can('hotel.create');
        Route::post('/', 'store')->name('store')->can('hotel.create');
        Route::get('{id}', 'show')->name('show')->can('hotel.view');
        Route::get('{id}/edit', 'edit')->name('edit')->can('hotel.edit');
        Route::put('{id}', 'update')->name('update')->can('hotel.edit');
        Route::delete('{id}', 'destroy')->name('destroy')->can('hotel.delete');

        // Cities API
        Route::get('api/cities/{countryId}', 'getCities')->name('api.cities');

        // Inventory
        Route::controller(\Modules\Hotel\Http\Controllers\HotelInventoryController::class)->group(function () {
            Route::get('{hotel}/availability', 'show')->name('availability.show')->can('hotel.edit');
            Route::get('{hotel}/inventory-data', 'getData')->name('inventory.data')->can('hotel.edit');
            Route::post('{hotel}/inventory-update', 'update')->name('inventory.update')->can('hotel.edit');
        });

        // Rooms (nested under hotels)
        Route::prefix('{hotelId}/rooms')->controller(HotelRoomController::class)->name('room.')->group(function () {
            Route::get('/recycle-bin', 'recycleBin')->name('recycleBin')->can('hotel.room.view');
            Route::post('/recycle-bin/{roomId}/restore', 'restore')->name('restore')->can('hotel.room.edit');
            Route::delete('/recycle-bin/{roomId}/force', 'destroyForce')->name('destroyForce')->can('hotel.room.delete');

            Route::get('/', 'index')->name('index')->can('hotel.room.view');
            Route::get('create', 'create')->name('create')->can('hotel.room.create');
            Route::post('/', 'store')->name('store')->can('hotel.room.create');
            Route::get('{roomId}', 'show')->name('show')->can('hotel.room.view');
            Route::get('{roomId}/edit', 'edit')->name('edit')->can('hotel.room.edit');
            Route::put('{roomId}', 'update')->name('update')->can('hotel.room.edit');
            Route::delete('{roomId}', 'destroy')->name('destroy')->can('hotel.room.delete');
        });
    });

    // Hotel Bookings
    Route::prefix('hotel-bookings')->controller(HotelBookingController::class)->name('hotelBooking.')->group(function () {
        // Recycle Bin
        Route::get('/recycle-bin', 'recycleBin')->name('recycleBin')->can('hotel.booking.view');
        Route::post('/recycle-bin/{id}/restore', 'restore')->name('restore')->can('hotel.booking.edit');
        Route::delete('/recycle-bin/{id}/force', 'destroyForce')->name('destroyForce')->can('hotel.booking.delete');
        Route::delete('/recycle-bin/empty', 'emptyRecycleBin')->name('emptyRecycleBin')->can('hotel.booking.delete');
        Route::post('/recycle-bin/restore-all', 'restoreRecycleBin')->name('restoreRecycleBin')->can('hotel.booking.edit');

        // Bulk Actions
        Route::post('/bulk-destroy', 'bulkDestroy')->name('bulkDestroy')->can('hotel.booking.delete');
        Route::post('/bulk-status', 'bulkUpdateStatus')->name('bulkUpdateStatus')->can('hotel.booking.edit');

        // Standard CRUD
        Route::get('/', 'index')->name('index')->can('hotel.booking.view');
        Route::get('create', 'create')->name('create')->can('hotel.booking.create');
        Route::post('/', 'store')->name('store')->can('hotel.booking.create');
        Route::get('{id}', 'show')->name('show')->can('hotel.booking.view');
        Route::get('{id}/edit', 'edit')->name('edit')->can('hotel.booking.edit');
        Route::put('{id}', 'update')->name('update')->can('hotel.booking.edit');
        Route::patch('{id}/status/{status}', 'updateStatus')->name('updateStatus')->can('hotel.booking.edit');
        Route::post('{id}/payment', 'addPayment')->name('addPayment')->can('hotel.payment.create');
        Route::patch('{id}/payment/{paymentId}/verify', 'verifyPayment')->name('verifyPayment')->can('hotel.payment.create');
        Route::delete('{id}', 'destroy')->name('destroy')->can('hotel.booking.delete');
    });

    // Hotel Reviews
    Route::prefix('hotel-reviews')->controller(HotelReviewController::class)->name('hotelReview.')->group(function () {
        Route::get('/', 'index')->name('index')->can('hotel.review.view');
        Route::get('recycle-bin', 'recycleBin')->name('recycleBin')->can('hotel.review.delete');
        Route::post('recycle-bin/restore', 'restoreRecycleBin')->name('restoreRecycleBin')->can('hotel.review.delete');
        Route::delete('recycle-bin/empty', 'emptyRecycleBin')->name('emptyRecycleBin')->can('hotel.review.delete');
        Route::get('{id}', 'show')->name('show')->can('hotel.review.view');
        Route::patch('{id}/approve', 'approve')->name('approve')->can('hotel.review.moderate');
        Route::patch('{id}/reject', 'reject')->name('reject')->can('hotel.review.moderate');
        Route::patch('{id}/toggle-featured', 'toggleFeatured')->name('toggleFeatured')->can('hotel.review.moderate');
        Route::post('{id}/response', 'addResponse')->name('addResponse')->can('hotel.review.respond');
        Route::post('{id}/restore', 'restore')->name('restore')->can('hotel.review.delete');
        Route::delete('{id}/force', 'destroyForce')->name('destroyForce')->can('hotel.review.delete');
        Route::delete('{id}', 'destroy')->name('destroy')->can('hotel.review.delete');
    });

});
