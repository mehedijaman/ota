<?php

use Illuminate\Support\Facades\Route;
use Modules\CustomerDashboard\Http\Controllers\CustomerDashboardController;

Route::middleware(['auth.customer'])->group(function () {
    Route::get('/customer/dashboard', [
        CustomerDashboardController::class, 'index',
    ])->name('customerDashboard.index');
});
