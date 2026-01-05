<?php

namespace Modules\Sms\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;
use Modules\Sms\Models\SmsPackagePurchase;
use Modules\Sms\Services\SmsPurchaseService;
use Modules\Support\Http\Controllers\BackendController;

class SuperadminSmsPurchaseController extends BackendController
{
    public function index(Request $request): Response
    {
        $status = $request->get('status', 'pending');

        $purchases = SmsPackagePurchase::query()
            ->with(['package', 'tenant'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest('id')
            ->paginate($request->rowsPerPage ?? 10)
            ->withQueryString()
            ->through(fn ($purchase) => [
                'id' => $purchase->id,
                'tenant_id' => $purchase->tenant_id,
                'tenant_name' => $purchase->tenant?->name,
                'tenant_slug' => $purchase->tenant?->slug,
                'package_name' => $purchase->package?->name,
                'quantity' => (int) $purchase->quantity,
                'total_credits' => (int) $purchase->total_credits,
                'total_price' => $purchase->total_price,
                'currency' => $purchase->currency,
                'status' => $purchase->status,
                'payment_method' => $purchase->payment_method,
                'payment_reference' => $purchase->payment_reference,
                'created_at' => $purchase->created_at,
                'approved_at' => $purchase->approved_at,
            ]);

        return inertia('Sms/Superadmin/PurchasesIndex', [
            'purchases' => $purchases,
            'status' => $status,
        ]);
    }

    public function approve(Request $request, int $id, SmsPurchaseService $purchaseService): RedirectResponse
    {
        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $purchaseService->approvePurchase($id, (int) Auth::id(), $validated['admin_notes'] ?? null);

        return back()->with('success', 'Purchase approved.');
    }

    public function reject(Request $request, int $id, SmsPurchaseService $purchaseService): RedirectResponse
    {
        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $purchaseService->rejectPurchase($id, (int) Auth::id(), $validated['admin_notes'] ?? null);

        return back()->with('success', 'Purchase rejected.');
    }
}
