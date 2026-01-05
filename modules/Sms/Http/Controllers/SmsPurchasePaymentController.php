<?php

namespace Modules\Sms\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Sms\Models\SmsPackagePurchase;
use Modules\Sms\Services\SmsPurchasePaymentService;
use Modules\Sms\Services\SmsPurchaseService;

class SmsPurchasePaymentController extends Controller
{
    public function __construct(
        protected SmsPurchasePaymentService $paymentService,
        protected SmsPurchaseService $purchaseService
    ) {}

    public function success(Request $request, string $transactionId)
    {
        $purchase = SmsPackagePurchase::query()
            ->where('transaction_id', $transactionId)
            ->firstOrFail();

        Log::info('SMS Purchase gateway success callback received', [
            'purchase_id' => $purchase->id,
            'transaction_id' => $transactionId,
            'gateway' => $purchase->payment_method,
            'payload' => $request->all(),
        ]);

        $payload = $request->all();
        $payload['tran_id'] = (string) ($payload['tran_id'] ?? $transactionId);
        $payload['amount'] = $payload['amount'] ?? (string) $purchase->total_price;
        $payload['currency'] = $payload['currency'] ?? ($purchase->currency ?? 'BDT');

        // SSLCommerz: tran_id is our transaction_id
        $gatewayTransactionId = (string) $payload['tran_id'];

        $ok = $this->paymentService->verifyAndComplete($purchase, $gatewayTransactionId, $payload);

        Log::info('SMS Purchase success verification completed', [
            'purchase_id' => $purchase->id,
            'transaction_id' => $transactionId,
            'gateway' => $purchase->payment_method,
            'ok' => $ok,
        ]);

        if ($ok) {
            $this->autoApproveIfEligible($purchase);

            $purchase->refresh();
            Log::info('SMS Purchase success post-approval state', [
                'purchase_id' => $purchase->id,
                'transaction_id' => $transactionId,
                'status' => $purchase->status,
                'payment_status' => $purchase->payment_status,
                'payment_method' => $purchase->payment_method,
                'gateway_transaction_id' => $purchase->gateway_transaction_id,
            ]);

            return redirect()->route('tenant.sms.wallet');
        }

        return redirect()->route('tenant.sms.wallet');
    }

    public function fail(Request $request, string $transactionId)
    {
        $purchase = SmsPackagePurchase::query()
            ->where('transaction_id', $transactionId)
            ->firstOrFail();

        $this->paymentService->markFailed($purchase, $request->all());

        return redirect()->route('tenant.sms.wallet');
    }

    public function userCancel(Request $request, string $transactionId)
    {
        $purchase = SmsPackagePurchase::query()
            ->where('transaction_id', $transactionId)
            ->firstOrFail();

        $this->paymentService->markCancelled($purchase, $request->all());

        return redirect()->route('tenant.sms.wallet');
    }

    public function callback(Request $request, string $transactionId)
    {
        $purchase = SmsPackagePurchase::query()
            ->where('transaction_id', $transactionId)
            ->firstOrFail();

        // bKash callback
        $paymentId = $request->paymentID;
        $status = $request->status;

        if ($status === 'success' && $paymentId) {
            $ok = $this->paymentService->verifyAndComplete($purchase, (string) $paymentId, $request->all());

            if ($ok) {
                $this->autoApproveIfEligible($purchase);

                return redirect()->route('tenant.sms.wallet');
            }
        }

        return redirect()->route('tenant.sms.wallet');
    }

    public function ipn(Request $request, string $transactionId)
    {
        $purchase = SmsPackagePurchase::query()
            ->where('transaction_id', $transactionId)
            ->firstOrFail();

        $payload = $request->all();
        $payload['tran_id'] = (string) ($payload['tran_id'] ?? $transactionId);
        $payload['amount'] = $payload['amount'] ?? (string) $purchase->total_price;
        $payload['currency'] = $payload['currency'] ?? ($purchase->currency ?? 'BDT');

        // SSLCommerz validates by tran_id (our transaction_id). Keep val_id in payload for validation.
        $gatewayTransactionId = (string) $payload['tran_id'];

        $ok = $this->paymentService->verifyAndComplete($purchase, $gatewayTransactionId, $payload);

        Log::info('SMS Purchase ipn verification completed', [
            'purchase_id' => $purchase->id,
            'transaction_id' => $transactionId,
            'gateway' => $purchase->payment_method,
            'ok' => $ok,
        ]);

        if ($ok) {
            $this->autoApproveIfEligible($purchase);

            $purchase->refresh();
            Log::info('SMS Purchase ipn post-approval state', [
                'purchase_id' => $purchase->id,
                'transaction_id' => $transactionId,
                'status' => $purchase->status,
                'payment_status' => $purchase->payment_status,
                'payment_method' => $purchase->payment_method,
                'gateway_transaction_id' => $purchase->gateway_transaction_id,
            ]);
        }

        return response()->json(['status' => 'success']);
    }

    private function autoApproveIfEligible(SmsPackagePurchase $purchase): void
    {
        $purchase->refresh();

        if ($purchase->status !== 'pending') {
            Log::info('SMS Purchase auto-approve skipped (status not pending)', [
                'purchase_id' => $purchase->id,
                'status' => $purchase->status,
                'payment_status' => $purchase->payment_status,
                'payment_method' => $purchase->payment_method,
            ]);

            return;
        }

        $method = strtolower((string) $purchase->payment_method);
        if (! in_array($method, ['sslcommerz', 'bkash'], true)) {
            Log::info('SMS Purchase auto-approve skipped (unsupported method)', [
                'purchase_id' => $purchase->id,
                'payment_method' => $purchase->payment_method,
                'payment_status' => $purchase->payment_status,
            ]);

            return;
        }

        if (($purchase->payment_status ?? null) !== 'paid') {
            Log::info('SMS Purchase auto-approve skipped (not paid)', [
                'purchase_id' => $purchase->id,
                'payment_method' => $purchase->payment_method,
                'payment_status' => $purchase->payment_status,
            ]);

            return;
        }

        try {
            $this->purchaseService->approvePurchase(
                purchaseId: $purchase->id,
                approvedBy: null,
                adminNotes: 'Auto-approved via gateway payment.'
            );
        } catch (\Throwable $e) {
            Log::warning('SMS Purchase auto-approval failed', [
                'purchase_id' => $purchase->id,
                'error' => $e->getMessage(),
                'exception' => $e,
            ]);
        }
    }
}
