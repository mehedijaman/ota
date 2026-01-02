<?php

use Illuminate\Support\Facades\Route;
use Modules\Hotel\Http\Controllers\HotelController;

Route::get('hotel', [
    HotelController::class, 'index', 
])->name('hotel.index');

Route::get('hotel/create', [
    HotelController::class, 'create',
])->name('hotel.create');

Route::post('hotel', [
    HotelController::class, 'store',
])->name('hotel.store');

Route::get('hotel/{id}/edit', [
    HotelController::class, 'edit',
])->name('hotel.edit');

Route::put('hotel/{id}', [
    HotelController::class, 'update',
])->name('hotel.update');

Route::delete('hotel/{id}', [
    HotelController::class, 'destroy',
])->name('hotel.destroy');
