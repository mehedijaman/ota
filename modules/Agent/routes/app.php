<?php

use Illuminate\Support\Facades\Route;
use Modules\Agent\Http\Controllers\AgentController;

Route::get('agent', [
    AgentController::class, 'index',
])->name('agent.index');

Route::get('agent/create', [
    AgentController::class, 'create',
])->name('agent.create');

Route::post('agent', [
    AgentController::class, 'store',
])->name('agent.store');

Route::get('agent/{id}/edit', [
    AgentController::class, 'edit',
])->name('agent.edit');

Route::put('agent/{id}', [
    AgentController::class, 'update',
])->name('agent.update');

Route::delete('agent/{id}', [
    AgentController::class, 'destroy',
])->name('agent.destroy');
