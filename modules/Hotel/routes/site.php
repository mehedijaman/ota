<?php

use Illuminate\Support\Facades\Route;
use Modules\Hotel\Http\Controllers\SiteHotelController;

Route::get('hotels', [SiteHotelController::class, 'index'])->name('site.hotel.index');
Route::get('hotels/search', [SiteHotelController::class, 'search'])->name('site.hotel.search');
Route::get('hotels/{slug}', [SiteHotelController::class, 'show'])->name('site.hotel.show');

// Authenticated booking routes
Route::middleware('auth')->group(function () {
    Route::get('/hotels/{slug}/book', [SiteHotelController::class, 'bookingPage'])->name('site.hotel.booking');
    Route::post('/hotels/{slug}/book', [SiteHotelController::class, 'submitBooking'])->name('site.hotel.submitBooking');
    Route::get('/hotels/booking/{booking}/payment', [SiteHotelController::class, 'paymentPage'])->name('site.hotel.payment');
    Route::post('/hotels/booking/{booking}/payment', [SiteHotelController::class, 'processPayment'])->name('site.hotel.processPayment');
    Route::get('/hotels/{slug}/booking/{booking}/confirmation', [SiteHotelController::class, 'bookingConfirmation'])->name('site.hotel.bookingConfirmation');
    Route::post('hotels/{slug}/review', [SiteHotelController::class, 'submitReview'])->name('site.hotel.submitReview');
});

// Payment callback routes (no auth required for gateway callbacks)
Route::post('/hotels/payment/{booking}/success', [SiteHotelController::class, 'paymentSuccess'])->name('site.hotel.payment.success');
Route::post('/hotels/payment/{booking}/fail', [SiteHotelController::class, 'paymentFail'])->name('site.hotel.payment.fail');
Route::post('/hotels/payment/{booking}/cancel', [SiteHotelController::class, 'paymentCancel'])->name('site.hotel.payment.cancel');
Route::post('/hotels/payment/{booking}/ipn', [SiteHotelController::class, 'paymentIPN'])->name('site.hotel.payment.ipn');
Route::get('/hotels/payment/{booking}/bkash/callback', [SiteHotelController::class, 'bkashCallback'])->name('site.hotel.payment.bkash.callback');
