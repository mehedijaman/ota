<?php

use Illuminate\Support\Facades\Route;
use Modules\AdminAuth\Http\Controllers\ProfileController;

Route::middleware(['auth.user'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
