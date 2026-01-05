<?php

use Illuminate\Support\Facades\Route;
use Modules\Tour\Http\Controllers\TourController;

Route::get('tour', [
    TourController::class, 'index',
])->name('tour.index');

Route::get('tour/create', [
    TourController::class, 'create',
])->name('tour.create');

Route::post('tour', [
    TourController::class, 'store',
])->name('tour.store');

Route::get('tour/{id}/edit', [
    TourController::class, 'edit',
])->name('tour.edit');

Route::put('tour/{id}', [
    TourController::class, 'update',
])->name('tour.update');

Route::delete('tour/{id}', [
    TourController::class, 'destroy',
])->name('tour.destroy');
