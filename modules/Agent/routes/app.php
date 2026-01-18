<?php

use Illuminate\Support\Facades\Route;
use Modules\Agent\Http\Controllers\AgentController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('agents')->controller(AgentController::class)->name('agent.')->group(function () {

        // Recycle Bin
        Route::get('/recycle-bin', 'recycleBin')->name('recycleBin')->can('agent-delete');
        Route::post('/recycle-bin/{id}/restore', 'restore')->name('restore')->can('agent-delete');
        Route::delete('/recycle-bin/{id}/force', 'destroyForce')->name('destroyForce')->can('agent-delete');
        Route::delete('/recycle-bin/empty', 'emptyRecycleBin')->name('emptyRecycleBin')->can('agent-delete');
        Route::post('/recycle-bin/restore-all', 'restoreRecycleBin')->name('restoreRecycleBin')->can('agent-delete');

        // Standard CRUD
        Route::get('/', 'index')->name('index')->can('agent-list');
        Route::get('create', 'create')->name('create')->can('agent-create');
        Route::get('{agent:uuid}', 'show')->name('show')->can('agent-view');
        Route::post('/', 'store')->name('store')->can('agent-create');
        Route::get('{agent:uuid}/edit', 'edit')->name('edit')->can('agent-edit');
        Route::put('{agent:uuid}', 'update')->name('update')->can('agent-edit');
        Route::delete('{agent:uuid}', 'destroy')->name('destroy')->can('agent-delete');
        Route::post('{agent:uuid}/create-user', 'createUser')->name('createUser')->can('agent-edit');
        
        // Wallet
        Route::post('{agent:uuid}/wallet/deposit', [\Modules\Agent\Http\Controllers\AgentWalletController::class, 'store'])->name('wallet.deposit')->can('agent-edit');
        Route::put('{agent:uuid}/wallet/transactions/{transaction}', [\Modules\Agent\Http\Controllers\AgentWalletController::class, 'update'])->name('wallet.transaction.update')->can('agent-edit');
        Route::delete('{agent:uuid}/wallet/transactions/{transaction}', [\Modules\Agent\Http\Controllers\AgentWalletController::class, 'destroy'])->name('wallet.transaction.destroy')->can('agent-edit');
    });
});
