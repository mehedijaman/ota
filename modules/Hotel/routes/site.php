<?php

use Illuminate\Support\Facades\Route;
use Modules\Hotel\Http\Controllers\SiteHotelController;

Route::get('hotel', [SiteHotelController::class, 'index'])->name('site.hotel.index');
Route::get('hotel/search', [SiteHotelController::class, 'search'])->name('site.hotel.search');
Route::get('hotel/{id}', [SiteHotelController::class, 'show'])->name('site.hotel.show');
