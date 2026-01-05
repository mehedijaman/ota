<?php

namespace Modules\Sms\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Modules\Sms\Services\SmsPurchasePaymentService;
use Modules\Sms\Services\SmsPurchaseService;
use Modules\Support\Http\Controllers\BackendController;
use Symfony\Component\HttpFoundation\Response;

class TenantSmsPurchaseController extends BackendController
{
    public function store(Request $request, SmsPurchaseService $purchaseService, SmsPurchasePaymentService $paymentService): Response|RedirectResponse
    {
        $tenantId = session('active_tenant_id') ?? Auth::user()?->active_tenant_id;
        abort_if(! $tenantId, 403);

        $validated = $request->validate([
            'sms_package_id' => 'required|integer|exists:sms_packages,id',
            'quantity' => 'nullable|integer|min:1|max:1000',
            'payment_method' => 'nullable|string|in:manual,sslcommerz,bkash|max:30',
            'payment_reference' => 'nullable|string|max:255|required_if:payment_method,manual',
            'payment_notes' => 'nullable|string|max:2000',
        ]);

        $paymentMethod = ($validated['payment_method'] ?? null) ?: 'manual';

        $purchase = $purchaseService->createTenantPurchase(
            tenantId: (int) $tenantId,
            userId: Auth::id(),
            packageId: (int) $validated['sms_package_id'],
            quantity: (int) ($validated['quantity'] ?? 1),
            payment: [
                'method' => $paymentMethod,
                'reference' => $validated['payment_reference'] ?? null,
                'notes' => $validated['payment_notes'] ?? null,
            ]
        );

        if ($paymentMethod === 'manual') {
            return redirect()->route('tenant.sms.wallet')
                ->with('success', 'Purchase request submitted.');
        }

        $urls = [
            'success_url' => route('sms.purchases.payments.success', $purchase->transaction_id),
            'fail_url' => route('sms.purchases.payments.fail', $purchase->transaction_id),
            'cancel_url' => route('sms.purchases.payments.cancel', $purchase->transaction_id),
            'callback_url' => route('sms.purchases.payments.callback', $purchase->transaction_id),
            'ipn_url' => route('sms.purchases.payments.ipn', $purchase->transaction_id),
        ];

        $result = $paymentService->startGatewayPayment($purchase, $urls);

        if ($result['success'] ?? false) {
            if ($paymentMethod === 'sslcommerz') {
                return Inertia::location($result['gateway_url']);
            }

            return Inertia::location($result['bkash_url']);
        }

        return redirect()->route('tenant.sms.wallet')
            ->with('error', $result['message'] ?? 'Payment initiation failed.');
    }
}
