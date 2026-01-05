<?php

namespace Modules\Sms\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Response;
use Modules\Customer\Models\Customer;
use Modules\Sms\Exceptions\InsufficientSmsBalanceException;
use Modules\Sms\Models\SmsTenantSetting;
use Modules\Sms\Services\SmsSendingService;
use Modules\Support\Http\Controllers\BackendController;

class TenantSmsDueReminderController extends BackendController
{
    public function create(): Response
    {
        $tenantId = session('active_tenant_id') ?? Auth::user()?->active_tenant_id;
        abort_if(! $tenantId, 403);

        $settings = SmsTenantSetting::query()->where('tenant_id', (int) $tenantId)->first();
        $defaultTemplate = trim((string) ($settings?->due_reminder_template ?? ''));
        if ($defaultTemplate === '') {
            $defaultTemplate = 'Dear {name}, your due is {due}. Please pay as soon as possible.';
        }

        $customers = Customer::query()
            ->with('primaryContact')
            ->where('tenant_id', (int) $tenantId)
            ->where('balance', '>', 0)
            ->whereHas('primaryContact', function ($q) {
                $q->whereNotNull('phone')->where('phone', '!=', '');
            })
            ->orderBy('name')
            ->limit(500)
            ->get()
            ->map(fn ($c) => [
                'value' => $c->id,
                'label' => $c->name.' (Due: '.number_format((float) $c->balance, 2).')',
                'phone' => $c->primaryContact?->phone,
                'name' => $c->name,
                'due' => $c->balance,
            ]);

        return inertia('Sms/Tenant/DueReminder', [
            'customerOptions' => $customers,
            'defaultTemplate' => $defaultTemplate,
        ]);
    }

    public function store(Request $request, SmsSendingService $sending): RedirectResponse
    {
        $tenantId = session('active_tenant_id') ?? Auth::user()?->active_tenant_id;
        abort_if(! $tenantId, 403);

        $validated = $request->validate([
            'customer_ids' => 'required|array|min:1|max:200',
            'customer_ids.*' => [
                'required',
                'integer',
                Rule::exists('customers', 'id')->where(function ($q) use ($tenantId) {
                    $q->where('tenant_id', (int) $tenantId)
                        ->where('balance', '>', 0);
                }),
            ],
            'template' => 'required|string|max:1000',
        ]);

        $customers = Customer::query()
            ->with('primaryContact')
            ->where('tenant_id', (int) $tenantId)
            ->where('balance', '>', 0)
            ->whereIn('id', $validated['customer_ids'])
            ->get();

        $sent = 0;
        $failed = 0;
        $firstFailure = null;

        foreach ($customers as $customer) {
            $phone = $customer->primaryContact?->phone;
            if (! $phone) {
                $failed++;
                $firstFailure ??= 'Missing phone number for: '.(string) $customer->name;

                continue;
            }

            $message = str_replace(
                ['{name}', '{due}'],
                [(string) $customer->name, (string) $customer->balance],
                (string) $validated['template']
            );

            try {
                $sms = $sending->sendNow(
                    tenantId: (int) $tenantId,
                    userId: Auth::id(),
                    type: 'due_reminder',
                    toPhone: (string) $phone,
                    message: $message,
                    meta: ['customer_id' => $customer->id]
                );
            } catch (InsufficientSmsBalanceException $e) {
                return redirect()->route('tenant.sms.wallet')
                    ->with('error', $e->getMessage());
            }

            if ($sms->status === 'sent') {
                $sent++;
            } else {
                $failed++;
                $firstFailure ??= (string) ($sms->error_message ?: 'Unknown SMS error');
            }
        }

        $flash = "Due reminders processed. Sent: {$sent}, Failed: {$failed}.";
        if ($firstFailure) {
            $flash .= ' First failure: '.$firstFailure;
        }

        return redirect()->route('tenant.sms.wallet')
            ->with('success', $flash);
    }
}
