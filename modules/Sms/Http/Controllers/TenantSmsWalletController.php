<?php

namespace Modules\Sms\Http\Controllers;

use Inertia\Response;
use Modules\Sms\Models\SmsPackage;
use Modules\Sms\Models\SmsPackagePurchase;
use Modules\Sms\Services\SmsWalletService;
use Modules\Support\Http\Controllers\BackendController;

class TenantSmsWalletController extends BackendController
{
    public function index(SmsWalletService $wallet): Response
    {
        $tenantId = session('active_tenant_id') ?? auth()->user()?->active_tenant_id;
        abort_if(! $tenantId, 403);

        $packages = SmsPackage::query()
            ->where('is_active', true)
            ->orderBy('price')
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'description' => $p->description,
                'sms_credits' => (int) $p->sms_credits,
                'duration_days' => (int) $p->duration_days,
                'price' => $p->price,
                'currency' => $p->currency,
            ]);

        $purchases = SmsPackagePurchase::query()
            ->where('tenant_id', $tenantId)
            ->with('package')
            ->latest('id')
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($purchase) => [
                'id' => $purchase->id,
                'package_name' => $purchase->package?->name,
                'quantity' => (int) $purchase->quantity,
                'total_credits' => (int) $purchase->total_credits,
                'total_price' => $purchase->total_price,
                'currency' => $purchase->currency,
                'status' => $purchase->status,
                'starts_at' => $purchase->starts_at,
                'expires_at' => $purchase->expires_at,
                'created_at' => $purchase->created_at,
            ]);

        return inertia('Sms/Tenant/Wallet', [
            'wallet' => [
                'available_credits' => $wallet->getAvailableCredits((int) $tenantId),
                'reserved_credits' => $wallet->getReservedCredits((int) $tenantId),
            ],
            'packages' => $packages,
            'purchases' => $purchases,
        ]);
    }
}
