<?php

use Illuminate\Support\Facades\Route;
use Modules\Flight\Http\Controllers\FlightSearchController;
use Modules\Flight\Http\Controllers\SiteFlightController;

// Public Flight Routes (no auth required)
Route::prefix('flight')->name('flight.')->group(function () {
    // Search page
    Route::get('/', [SiteFlightController::class, 'index'])->name('home');

    // Search results
    Route::get('/results', [SiteFlightController::class, 'results'])->name('results');

    // API endpoints
    Route::get('/api/airports', [FlightSearchController::class, 'searchAirports'])->name('api.airports');
    Route::get('/api/popular-airports', [FlightSearchController::class, 'popularAirports'])->name('api.popularAirports');
});

// Protected routes (customer must log in to book)
Route::prefix('flight')->name('flight.')->middleware(['auth'])->group(function () {
    // Booking flow
    Route::get('/booking', [SiteFlightController::class, 'booking'])->name('booking');
    Route::post('/booking', [SiteFlightController::class, 'storeBooking'])->name('storeBooking');

    // Payment
    Route::get('/payment/{booking}', [SiteFlightController::class, 'payment'])->name('payment');
    Route::post('/payment/{booking}', [SiteFlightController::class, 'processPayment'])->name('processPayment');

    // Confirmation
    Route::get('/confirmation/{booking}', [SiteFlightController::class, 'confirmation'])->name('confirmation');

    // My Bookings
    Route::get('/my-bookings', [SiteFlightController::class, 'myBookings'])->name('myBookings');
    Route::get('/my-bookings/{booking}', [SiteFlightController::class, 'viewBooking'])->name('viewBooking');
});


