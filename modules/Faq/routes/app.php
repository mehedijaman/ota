<?php

use Illuminate\Support\Facades\Route;
use Modules\Faq\Http\Controllers\FaqController;

Route::get('faq', [
    FaqController::class, 'index',
])->name('faq.index');

Route::get('faq/create', [
    FaqController::class, 'create',
])->name('faq.create');

Route::post('faq', [
    FaqController::class, 'store',
])->name('faq.store');

Route::get('faq/{id}/edit', [
    FaqController::class, 'edit',
])->name('faq.edit');

Route::put('faq/{id}', [
    FaqController::class, 'update',
])->name('faq.update');

Route::delete('faq/{id}', [
    FaqController::class, 'destroy',
])->name('faq.destroy');
