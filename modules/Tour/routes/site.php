<?php

use Illuminate\Support\Facades\Route;
use Modules\Tour\Http\Controllers\SiteTourController;

Route::get('tour', [SiteTourController::class, 'index'])->name('site.tour.index');
Route::get('tour/search', [SiteTourController::class, 'search'])->name('site.tour.search');
Route::get('tour/{slug}', [SiteTourController::class, 'show'])->name('site.tour.show');

// Authenticated booking routes
Route::middleware('auth')->group(function () {
    Route::get('/tours/{slug}/book', [SiteTourController::class, 'bookingPage'])->name('site.tour.booking');
    Route::post('/tours/{slug}/book', [SiteTourController::class, 'submitBooking'])->name('site.tour.submitBooking');
    Route::get('/tours/booking/{booking}/payment', [SiteTourController::class, 'paymentPage'])->name('site.tour.payment');
    Route::post('/tours/booking/{booking}/payment', [SiteTourController::class, 'processPayment'])->name('site.tour.processPayment');
    Route::get('/tours/{slug}/booking/{booking}/confirmation', [SiteTourController::class, 'bookingConfirmation'])->name('site.tour.bookingConfirmation');
    Route::post('tour/{slug}/review', [SiteTourController::class, 'submitReview'])->name('site.tour.submitReview');
    
});

// Payment callback routes (no auth required for gateway callbacks)
Route::get('/tours/payment/{booking}/success', [SiteTourController::class, 'paymentSuccess'])->name('site.tour.payment.success');
Route::get('/tours/payment/{booking}/fail', [SiteTourController::class, 'paymentFail'])->name('site.tour.payment.fail');
Route::get('/tours/payment/{booking}/cancel', [SiteTourController::class, 'paymentCancel'])->name('site.tour.payment.cancel');
Route::post('/tours/payment/{booking}/ipn', [SiteTourController::class, 'paymentIPN'])->name('site.tour.payment.ipn');

