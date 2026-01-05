<?php

use Illuminate\Support\Facades\Route;
use Modules\Flight\Http\Controllers\FlightController;

Route::get('flight', [
    FlightController::class, 'index',
])->name('flight.index');

Route::get('flight/create', [
    FlightController::class, 'create',
])->name('flight.create');

Route::post('flight', [
    FlightController::class, 'store',
])->name('flight.store');

Route::get('flight/{id}/edit', [
    FlightController::class, 'edit',
])->name('flight.edit');

Route::put('flight/{id}', [
    FlightController::class, 'update',
])->name('flight.update');

Route::delete('flight/{id}', [
    FlightController::class, 'destroy',
])->name('flight.destroy');
