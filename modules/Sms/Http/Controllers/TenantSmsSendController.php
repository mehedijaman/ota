<?php

namespace Modules\Sms\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Modules\Sms\Exceptions\InsufficientSmsBalanceException;
use Modules\Sms\Services\SmsSendingService;
use Modules\Support\Http\Controllers\BackendController;

class TenantSmsSendController extends BackendController
{
    public function create(): Response
    {
        return inertia('Sms/Tenant/SendPromotional');
    }

    public function store(Request $request, SmsSendingService $sending): RedirectResponse
    {
        $tenantId = session('active_tenant_id') ?? auth()->user()?->active_tenant_id;
        abort_if(! $tenantId, 403);

        $validated = $request->validate([
            'to_phone' => 'required|string|max:30',
            'message' => 'required|string|max:1000',
        ]);

        try {
            $sms = $sending->sendNow(
                tenantId: (int) $tenantId,
                userId: auth()->id(),
                type: 'promotional',
                toPhone: (string) $validated['to_phone'],
                message: (string) $validated['message'],
                meta: []
            );
        } catch (InsufficientSmsBalanceException $e) {
            return back()->with('error', $e->getMessage());
        }

        if ($sms->status === 'sent') {
            return back()->with('success', 'SMS sent.');
        }

        return back()->with('error', $sms->error_message ?: 'SMS failed.');
    }
}
