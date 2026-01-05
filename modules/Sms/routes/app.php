<?php

use Illuminate\Support\Facades\Route;
use Modules\Sms\Http\Controllers\SmsController;
use Modules\Sms\Http\Controllers\SmsPurchasePaymentController;
use Modules\Sms\Http\Controllers\SuperadminSmsPackageController;
use Modules\Sms\Http\Controllers\SuperadminSmsPurchaseController;
use Modules\Sms\Http\Controllers\SuperadminSmsSendController;
use Modules\Sms\Http\Controllers\SuperadminSmsSettingsController;
use Modules\Sms\Http\Controllers\SuperadminSmsTemplateController;
use Modules\Sms\Http\Controllers\TenantSmsDueReminderController;
use Modules\Sms\Http\Controllers\TenantSmsPurchaseController;
use Modules\Sms\Http\Controllers\TenantSmsSettingsController;
use Modules\Sms\Http\Controllers\TenantSmsWalletController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('sms', [
        SmsController::class,
        'index',
    ])->name('sms.index');

    Route::get('sms/create', [
        SmsController::class,
        'create',
    ])->name('sms.create');

    Route::post('sms', [
        SmsController::class,
        'store',
    ])->name('sms.store');

    Route::get('sms/{id}/edit', [
        SmsController::class,
        'edit',
    ])->name('sms.edit');

    Route::put('sms/{id}', [
        SmsController::class,
        'update',
    ])->name('sms.update');

    Route::delete('sms/{id}', [
        SmsController::class,
        'destroy',
    ])->name('sms.destroy');

    // Tenant SMS wallet & sending
    Route::get('tenant/sms/wallet', [
        TenantSmsWalletController::class,
        'index',
    ])->name('tenant.sms.wallet');

    Route::post('tenant/sms/purchases', [
        TenantSmsPurchaseController::class,
        'store',
    ])->name('tenant.sms.purchases.store');

    Route::get('tenant/sms/settings', [
        TenantSmsSettingsController::class,
        'edit',
    ])->name('tenant.sms.settings.edit');

    Route::put('tenant/sms/settings', [
        TenantSmsSettingsController::class,
        'update',
    ])->name('tenant.sms.settings.update');

    Route::get('tenant/sms/due-reminder', [
        TenantSmsDueReminderController::class,
        'create',
    ])->name('tenant.sms.due.create');

    Route::post('tenant/sms/due-reminder', [
        TenantSmsDueReminderController::class,
        'store',
    ])->name('tenant.sms.due.store');

    // SMS Reports
    Route::get('sms/report', [
        \Modules\Sms\Http\Controllers\SmsReportController::class,
        'index',
    ])->name('sms.report.index');

    Route::get('sms/report/generate', [
        \Modules\Sms\Http\Controllers\SmsReportController::class,
        'generate',
    ])->name('sms.report.generate');

    Route::get('sms/report/export', [
        \Modules\Sms\Http\Controllers\SmsReportController::class,
        'export',
    ])->name('sms.report.export');
});

// ====================================
// PAYMENT GATEWAY CALLBACKS (No Auth Required)
// ====================================
Route::prefix('sms-purchase-payments')
    ->name('sms.purchases.payments.')
    ->controller(SmsPurchasePaymentController::class)
    ->withoutMiddleware([
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
        \App\Http\Middleware\SetLocaleFromSession::class,
        \App\Http\Middleware\HandleInertiaRequests::class,
        \App\Http\Middleware\TenantMiddleware::class,
        \App\Http\Middleware\EnsureTenantContext::class,
    ])
    ->group(function () {
        Route::match(['get', 'post'], '{transactionId}/success', 'success')->name('success');
        Route::match(['get', 'post'], '{transactionId}/fail', 'fail')->name('fail');
        Route::match(['get', 'post'], '{transactionId}/cancel', 'userCancel')->name('cancel');
        Route::match(['get', 'post'], '{transactionId}/callback', 'callback')->name('callback');
        Route::post('{transactionId}/ipn', 'ipn')->name('ipn');
    });

Route::middleware(['auth', 'verified', 'superadmin'])
    ->prefix('superadmin')
    ->group(function () {
        // Promotional SMS sending (superadmin only)
        Route::get('sms/send', [
            SuperadminSmsSendController::class,
            'create',
        ])->name('superadmin.sms.send.create');

        Route::post('sms/send', [
            SuperadminSmsSendController::class,
            'store',
        ])->name('superadmin.sms.send.store');

        // Templates
        Route::get('sms/templates', [
            SuperadminSmsTemplateController::class,
            'index',
        ])->name('superadmin.sms.templates.index');

        Route::get('sms/templates/create', [
            SuperadminSmsTemplateController::class,
            'create',
        ])->name('superadmin.sms.templates.create');

        Route::post('sms/templates', [
            SuperadminSmsTemplateController::class,
            'store',
        ])->name('superadmin.sms.templates.store');

        Route::get('sms/templates/{smsTemplate}/edit', [
            SuperadminSmsTemplateController::class,
            'edit',
        ])->name('superadmin.sms.templates.edit');

        Route::put('sms/templates/{smsTemplate}', [
            SuperadminSmsTemplateController::class,
            'update',
        ])->name('superadmin.sms.templates.update');

        Route::delete('sms/templates/{smsTemplate}', [
            SuperadminSmsTemplateController::class,
            'destroy',
        ])->name('superadmin.sms.templates.destroy');

        // Packages
        Route::get('sms/packages', [
            SuperadminSmsPackageController::class,
            'index',
        ])->name('superadmin.sms.packages.index');

        Route::get('sms/packages/create', [
            SuperadminSmsPackageController::class,
            'create',
        ])->name('superadmin.sms.packages.create');

        Route::post('sms/packages', [
            SuperadminSmsPackageController::class,
            'store',
        ])->name('superadmin.sms.packages.store');

        Route::get('sms/packages/{smsPackage}/edit', [
            SuperadminSmsPackageController::class,
            'edit',
        ])->name('superadmin.sms.packages.edit');

        Route::put('sms/packages/{smsPackage}', [
            SuperadminSmsPackageController::class,
            'update',
        ])->name('superadmin.sms.packages.update');

        Route::delete('sms/packages/{smsPackage}', [
            SuperadminSmsPackageController::class,
            'destroy',
        ])->name('superadmin.sms.packages.destroy');

        // Purchase approvals
        Route::get('sms/purchases', [
            SuperadminSmsPurchaseController::class,
            'index',
        ])->name('superadmin.sms.purchases.index');

        Route::post('sms/purchases/{id}/approve', [
            SuperadminSmsPurchaseController::class,
            'approve',
        ])->name('superadmin.sms.purchases.approve');

        Route::post('sms/purchases/{id}/reject', [
            SuperadminSmsPurchaseController::class,
            'reject',
        ])->name('superadmin.sms.purchases.reject');

        Route::get('sms/settings', [
            SuperadminSmsSettingsController::class,
            'edit',
        ])->name('superadmin.sms.settings.edit');

        Route::put('sms/settings', [
            SuperadminSmsSettingsController::class,
            'update',
        ])->name('superadmin.sms.settings.update');

        Route::post('sms/settings/test', [
            SuperadminSmsSettingsController::class,
            'test',
        ])->name('superadmin.sms.settings.test');
    });
