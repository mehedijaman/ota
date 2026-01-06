<?php

use Illuminate\Support\Facades\Route;
use Modules\Sms\Http\Controllers\SmsCampaignController;
use Modules\Sms\Http\Controllers\SmsGatewayController;
use Modules\Sms\Http\Controllers\SmsReportController;
use Modules\Sms\Http\Controllers\SmsSendController;
use Modules\Sms\Http\Controllers\SmsTemplateController;

// SMS Gateway Settings Routes
Route::get('sms/gateways', [SmsGatewayController::class, 'index'])
    ->name('sms.gateways.index')
    ->can('sms-gateway-list');

Route::get('sms/gateways/create', [SmsGatewayController::class, 'create'])
    ->name('sms.gateways.create')
    ->can('sms-gateway-create');

Route::post('sms/gateways', [SmsGatewayController::class, 'store'])
    ->name('sms.gateways.store')
    ->can('sms-gateway-create');

Route::get('sms/gateways/{smsGateway}/edit', [SmsGatewayController::class, 'edit'])
    ->name('sms.gateways.edit')
    ->can('sms-gateway-edit');

Route::put('sms/gateways/{smsGateway}', [SmsGatewayController::class, 'update'])
    ->name('sms.gateways.update')
    ->can('sms-gateway-edit');

Route::delete('sms/gateways/{smsGateway}', [SmsGatewayController::class, 'destroy'])
    ->name('sms.gateways.destroy')
    ->can('sms-gateway-delete');

Route::post('sms/gateways/{smsGateway}/set-default', [SmsGatewayController::class, 'setDefault'])
    ->name('sms.gateways.setDefault')
    ->can('sms-gateway-edit');

Route::post('sms/gateways/{smsGateway}/test', [SmsGatewayController::class, 'test'])
    ->name('sms.gateways.test')
    ->can('sms-gateway-edit');

// SMS Template Routes
Route::get('sms/templates', [SmsTemplateController::class, 'index'])
    ->name('sms.templates.index')
    ->can('sms-template-list');

Route::get('sms/templates/create', [SmsTemplateController::class, 'create'])
    ->name('sms.templates.create')
    ->can('sms-template-create');

Route::post('sms/templates', [SmsTemplateController::class, 'store'])
    ->name('sms.templates.store')
    ->can('sms-template-create');

Route::get('sms/templates/{smsTemplate}/edit', [SmsTemplateController::class, 'edit'])
    ->name('sms.templates.edit')
    ->can('sms-template-edit');

Route::put('sms/templates/{smsTemplate}', [SmsTemplateController::class, 'update'])
    ->name('sms.templates.update')
    ->can('sms-template-edit');

Route::delete('sms/templates/{smsTemplate}', [SmsTemplateController::class, 'destroy'])
    ->name('sms.templates.destroy')
    ->can('sms-template-delete');

// SMS Campaign / Bulk Send Routes
Route::get('sms/campaigns', [SmsCampaignController::class, 'index'])
    ->name('sms.campaigns.index')
    ->can('sms-campaign-list');

Route::get('sms/campaigns/create', [SmsCampaignController::class, 'create'])
    ->name('sms.campaigns.create')
    ->can('sms-campaign-create');

Route::post('sms/campaigns', [SmsCampaignController::class, 'store'])
    ->name('sms.campaigns.store')
    ->can('sms-campaign-create');

Route::get('sms/campaigns/{smsCampaign}', [SmsCampaignController::class, 'show'])
    ->name('sms.campaigns.show')
    ->can('sms-campaign-view');

// SMS Send (Quick Send)
Route::get('sms/send', [SmsSendController::class, 'create'])
    ->name('sms.send.create')
    ->can('sms-send');

Route::post('sms/send', [SmsSendController::class, 'store'])
    ->name('sms.send.store')
    ->can('sms-send');

// SMS Reports
Route::get('sms/reports', [SmsReportController::class, 'index'])
    ->name('sms.reports.index')
    ->can('sms-report-list');

Route::get('sms/reports/export', [SmsReportController::class, 'export'])
    ->name('sms.reports.export')
    ->can('sms-report-list');
