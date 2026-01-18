<?php

use Illuminate\Support\Facades\Route;
use Modules\Flight\Http\Controllers\FlightBookingController;
use Modules\Flight\Http\Controllers\FlightController;
use Modules\Flight\Http\Controllers\FlightSearchController;

Route::middleware(['auth', 'verified'])->group(function () {

    // ==================== AIRPORTS ====================
    Route::prefix('flight/airports')->controller(FlightController::class)->name('flight.airport.')->group(function () {
        Route::get('/', 'airportIndex')->name('index')->can('flight.airport.view');
        Route::get('/create', 'airportCreate')->name('create')->can('flight.airport.create');
        Route::post('/', 'airportStore')->name('store')->can('flight.airport.create');
        Route::get('/{id}/edit', 'airportEdit')->name('edit')->can('flight.airport.edit');
        Route::put('/{id}', 'airportUpdate')->name('update')->can('flight.airport.edit');
        Route::delete('/{id}', 'airportDestroy')->name('destroy')->can('flight.airport.delete');
    });

    // ==================== MASTER DATA DASHBOARD ====================
    Route::prefix('flight')->controller(FlightController::class)->name('flight.')->group(function () {
        Route::get('/', 'index')->name('index')->can('flight.view');
    });

    // ==================== AIRLINES ====================
    Route::prefix('flight/airlines')->controller(FlightController::class)->name('flight.airline.')->group(function () {
        Route::get('/', 'airlineIndex')->name('index')->can('flight.airline.view');
        Route::get('/create', 'airlineCreate')->name('create')->can('flight.airline.create');
        Route::post('/', 'airlineStore')->name('store')->can('flight.airline.create');
        Route::get('/{id}/edit', 'airlineEdit')->name('edit')->can('flight.airline.edit');
        Route::put('/{id}', 'airlineUpdate')->name('update')->can('flight.airline.edit');
        Route::delete('/{id}', 'airlineDestroy')->name('destroy')->can('flight.airline.delete');
    });

    // ==================== AIRCRAFT TYPES ====================
    Route::prefix('flight/aircraft')->controller(FlightController::class)->name('flight.aircraft.')->group(function () {
        Route::get('/', 'aircraftIndex')->name('index')->can('flight.aircraft.view');
    });

    // ==================== SUPPLIERS ====================
    Route::prefix('flight/suppliers')->controller(FlightController::class)->name('flight.supplier.')->group(function () {
        Route::get('/', 'supplierIndex')->name('index')->can('flight.supplier.view');
    });

    // ==================== FARE CLASSES ====================
    Route::prefix('flight/fare-classes')->controller(FlightController::class)->name('flight.fareClass.')->group(function () {
        Route::get('/', 'fareClassIndex')->name('index')->can('flight.fare-class.view');
    });

    // ==================== API ENDPOINTS ====================
    Route::prefix('flight/api')->controller(FlightController::class)->name('flight.api.')->group(function () {
        Route::get('/airports', 'apiSearchAirports')->name('airports');
        Route::get('/airlines', 'apiSearchAirlines')->name('airlines');
    });

    Route::prefix('flight/api')->controller(FlightSearchController::class)->name('flight.api.')->group(function () {
        Route::post('/search', 'search')->name('search');
        Route::post('/filter', 'filter')->name('filter');
    });

    // ==================== BOOKINGS ====================
    Route::prefix('flight-bookings')->controller(FlightBookingController::class)->name('flightBooking.')->group(function () {
        // Recycle Bin
        Route::get('/recycle-bin', 'recycleBin')->name('recycleBin')->can('flight.booking.view');
        Route::post('/recycle-bin/{id}/restore', 'restore')->name('restore')->can('flight.booking.edit');
        Route::delete('/recycle-bin/{id}/force', 'destroyForce')->name('destroyForce')->can('flight.booking.delete');

        // Bulk Actions
        Route::post('/bulk-destroy', 'bulkDestroy')->name('bulkDestroy')->can('flight.booking.delete');
        Route::post('/bulk-status', 'bulkUpdateStatus')->name('bulkUpdateStatus')->can('flight.booking.edit');

        // Standard CRUD
        Route::get('/', 'index')->name('index')->can('flight.booking.view');
        Route::get('/create', 'create')->name('create')->can('flight.booking.create');
        Route::post('/', 'store')->name('store')->can('flight.booking.create');
        Route::get('/{id}', 'show')->name('show')->can('flight.booking.view');
        Route::get('/{id}/edit', 'edit')->name('edit')->can('flight.booking.edit');
        Route::put('/{id}', 'update')->name('update')->can('flight.booking.edit');
        Route::delete('/{id}', 'destroy')->name('destroy')->can('flight.booking.delete');

        // Status Management
        Route::patch('/{id}/status/{status}', 'updateStatus')->name('updateStatus')->can('flight.booking.edit');
        Route::patch('/{id}/confirm', 'confirm')->name('confirm')->can('flight.booking.edit');

        // Ticketing
        Route::post('/{id}/issue-ticket', 'issueTicket')->name('issueTicket')->can('flight.booking.ticket');
        Route::post('/{id}/void-ticket', 'voidTicket')->name('voidTicket')->can('flight.booking.void');

        // Payments
        Route::post('/{id}/payment', 'addPayment')->name('addPayment')->can('flight.payment.create');
        Route::patch('/{id}/payment/{paymentId}/verify', 'verifyPayment')->name('verifyPayment')->can('flight.payment.verify');

        // Cancellation
        Route::post('/{id}/cancel', 'requestCancellation')->name('requestCancellation')->can('flight.cancellation.create');
        Route::patch('/{id}/cancel/approve', 'approveCancellation')->name('approveCancellation')->can('flight.cancellation.approve');
        Route::post('/{id}/refund', 'processRefund')->name('processRefund')->can('flight.payment.refund');
    });

});
