<?php

namespace Modules\Sms\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Modules\Sms\Models\SmsTenantSetting;
use Modules\Support\Http\Controllers\BackendController;

class TenantSmsSettingsController extends BackendController
{
    public function edit(): Response
    {
        $tenantId = session('active_tenant_id') ?? auth()->user()?->active_tenant_id;
        abort_if(! $tenantId, 403);

        $settings = SmsTenantSetting::query()->firstOrCreate(
            ['tenant_id' => (int) $tenantId],
            [
                'sale_sms_enabled' => false,
                'sale_sms_template' => null,
                'invoice_sms_enabled' => false,
                'invoice_sms_template' => null,
                'due_reminder_sms_enabled' => false,
                'due_reminder_template' => null,
            ]
        );

        return inertia('Sms/Tenant/Settings', [
            'settings' => [
                'sale_sms_enabled' => (bool) $settings->sale_sms_enabled,
                'sale_sms_template' => (string) ($settings->sale_sms_template ?? ''),
                'invoice_sms_enabled' => (bool) $settings->invoice_sms_enabled,
                'invoice_sms_template' => (string) ($settings->invoice_sms_template ?? ''),
                'due_reminder_sms_enabled' => (bool) $settings->due_reminder_sms_enabled,
                'due_reminder_template' => (string) ($settings->due_reminder_template ?? ''),
            ],
            'defaults' => [
                'sale_sms_template' => 'Dear {name}, your sale {invoice_no} total {grand_total}. Paid {paid}, Due {due}. Thank you.',
                'invoice_sms_template' => 'Dear {name}, invoice {invoice_no} amount {grand_total}. Paid {paid}, Due {due}. Thank you.',
                'due_reminder_template' => 'Dear {name}, your due is {due}. Please pay as soon as possible.',
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $tenantId = session('active_tenant_id') ?? auth()->user()?->active_tenant_id;
        abort_if(! $tenantId, 403);

        $validated = $request->validate([
            'sale_sms_enabled' => ['nullable', 'boolean'],
            'sale_sms_template' => ['nullable', 'string', 'max:1000'],
            'invoice_sms_enabled' => ['nullable', 'boolean'],
            'invoice_sms_template' => ['nullable', 'string', 'max:1000'],
            'due_reminder_sms_enabled' => ['nullable', 'boolean'],
            'due_reminder_template' => ['nullable', 'string', 'max:1000'],
        ]);

        SmsTenantSetting::query()->updateOrCreate(
            ['tenant_id' => (int) $tenantId],
            [
                'sale_sms_enabled' => (bool) ($validated['sale_sms_enabled'] ?? false),
                'sale_sms_template' => $validated['sale_sms_template'] ?? null,
                'invoice_sms_enabled' => (bool) ($validated['invoice_sms_enabled'] ?? false),
                'invoice_sms_template' => $validated['invoice_sms_template'] ?? null,
                'due_reminder_sms_enabled' => (bool) ($validated['due_reminder_sms_enabled'] ?? false),
                'due_reminder_template' => $validated['due_reminder_template'] ?? null,
            ]
        );

        return redirect()->route('tenant.sms.settings.edit')
            ->with('success', 'SMS settings updated.');
    }
}
