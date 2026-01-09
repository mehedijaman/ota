<?php

use Illuminate\Support\Facades\Route;
use Modules\Tour\Http\Controllers\SiteTourController;

Route::get('tour', [SiteTourController::class, 'index'])->name('site.tour.index');
Route::get('tour/search', [SiteTourController::class, 'search'])->name('site.tour.search');
Route::get('tour/{slug}', [SiteTourController::class, 'show'])->name('site.tour.show');
Route::get('tour/{slug}/book', [SiteTourController::class, 'bookingPage'])->name('site.tour.booking');
Route::post('tour/{slug}/book', [SiteTourController::class, 'submitBooking'])->name('site.tour.submitBooking');
Route::get('tour/{slug}/confirmation/{booking}', [SiteTourController::class, 'bookingConfirmation'])->name('site.tour.bookingConfirmation');
Route::post('tour/{slug}/review', [SiteTourController::class, 'submitReview'])->name('site.tour.submitReview');
