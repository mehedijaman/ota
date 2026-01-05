<?php

use Illuminate\Support\Facades\Route;
use Modules\Visa\Http\Controllers\VisaController;

Route::get('visa', [
    VisaController::class, 'index',
])->name('visa.index');

Route::get('visa/create', [
    VisaController::class, 'create',
])->name('visa.create');

Route::post('visa', [
    VisaController::class, 'store',
])->name('visa.store');

Route::get('visa/{id}/edit', [
    VisaController::class, 'edit',
])->name('visa.edit');

Route::put('visa/{id}', [
    VisaController::class, 'update',
])->name('visa.update');

Route::delete('visa/{id}', [
    VisaController::class, 'destroy',
])->name('visa.destroy');
