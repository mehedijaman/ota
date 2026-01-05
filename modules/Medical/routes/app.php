<?php

use Illuminate\Support\Facades\Route;
use Modules\Medical\Http\Controllers\MedicalController;

Route::get('medical', [
    MedicalController::class, 'index',
])->name('medical.index');

Route::get('medical/create', [
    MedicalController::class, 'create',
])->name('medical.create');

Route::post('medical', [
    MedicalController::class, 'store',
])->name('medical.store');

Route::get('medical/{id}/edit', [
    MedicalController::class, 'edit',
])->name('medical.edit');

Route::put('medical/{id}', [
    MedicalController::class, 'update',
])->name('medical.update');

Route::delete('medical/{id}', [
    MedicalController::class, 'destroy',
])->name('medical.destroy');
