<?php

use Illuminate\Support\Facades\Route;
use Modules\AgentDashboard\Http\Controllers\AgentDashboardController;

Route::middleware(['auth.agent'])->group(function () {
    Route::get('/agent/dashboard', [
        AgentDashboardController::class, 'index',
    ])->name('agentDashboard.index');
});
