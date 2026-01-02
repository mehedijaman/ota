<?php

use Illuminate\Support\Facades\Route;
use Modules\Bill\Http\Controllers\BillController;

Route::get('bill', [
    BillController::class, 'index', 
])->name('bill.index');

Route::get('bill/create', [
    BillController::class, 'create',
])->name('bill.create');

Route::post('bill', [
    BillController::class, 'store',
])->name('bill.store');

Route::get('bill/{id}/edit', [
    BillController::class, 'edit',
])->name('bill.edit');

Route::put('bill/{id}', [
    BillController::class, 'update',
])->name('bill.update');

Route::delete('bill/{id}', [
    BillController::class, 'destroy',
])->name('bill.destroy');
