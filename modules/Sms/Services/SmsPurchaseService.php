<?php

namespace Modules\Sms\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Sms\Models\SmsCreditLot;
use Modules\Sms\Models\SmsPackage;
use Modules\Sms\Models\SmsPackagePurchase;
use RuntimeException;

class SmsPurchaseService
{
    public function createTenantPurchase(int $tenantId, ?int $userId, int $packageId, int $quantity = 1, array $payment = []): SmsPackagePurchase
    {
        $quantity = max(1, (int) $quantity);

        $package = SmsPackage::query()->whereKey($packageId)->where('is_active', true)->firstOrFail();

        return SmsPackagePurchase::create([
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'sms_package_id' => $package->id,
            'quantity' => $quantity,
            'total_credits' => (int) $package->sms_credits * $quantity,
            'total_price' => (float) $package->price * $quantity,
            'currency' => $package->currency ?? 'BDT',
            'status' => 'pending',
            'payment_method' => $payment['method'] ?? null,
            'payment_reference' => $payment['reference'] ?? null,
            'payment_notes' => $payment['notes'] ?? null,
            'transaction_id' => (string) Str::uuid(),
        ]);
    }

    public function approvePurchase(int $purchaseId, ?int $approvedBy, ?string $adminNotes = null): SmsPackagePurchase
    {
        return DB::transaction(function () use ($purchaseId, $approvedBy, $adminNotes) {
            /** @var SmsPackagePurchase $purchase */
            $purchase = SmsPackagePurchase::query()->lockForUpdate()->findOrFail($purchaseId);

            if ($purchase->status !== 'pending') {
                throw new RuntimeException('Only pending purchases can be approved.');
            }

            $package = SmsPackage::query()->whereKey($purchase->sms_package_id)->first();
            if (! $package) {
                throw new RuntimeException('Package not found for purchase.');
            }

            $startsAt = now();
            $expiresAt = now()->addDays((int) $package->duration_days);

            $purchase->status = 'approved';
            $purchase->approved_by = $approvedBy;
            $purchase->approved_at = now();
            $purchase->starts_at = $startsAt;
            $purchase->expires_at = $expiresAt;
            $purchase->admin_notes = $adminNotes;
            $purchase->save();

            SmsCreditLot::create([
                'tenant_id' => $purchase->tenant_id,
                'sms_package_purchase_id' => $purchase->id,
                'credits_total' => (int) $purchase->total_credits,
                'credits_used' => 0,
                'credits_reserved' => 0,
                'expires_at' => $expiresAt,
            ]);

            return $purchase;
        });
    }

    public function rejectPurchase(int $purchaseId, int $rejectedBy, ?string $adminNotes = null): SmsPackagePurchase
    {
        return DB::transaction(function () use ($purchaseId, $rejectedBy, $adminNotes) {
            /** @var SmsPackagePurchase $purchase */
            $purchase = SmsPackagePurchase::query()->lockForUpdate()->findOrFail($purchaseId);

            if ($purchase->status !== 'pending') {
                throw new RuntimeException('Only pending purchases can be rejected.');
            }

            $purchase->status = 'rejected';
            $purchase->approved_by = $rejectedBy;
            $purchase->approved_at = now();
            $purchase->admin_notes = $adminNotes;
            $purchase->save();

            return $purchase;
        });
    }
}
