<?php

namespace Modules\Sms\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Sms\Models\SmsPackagePurchase;

class SmsPurchasePaymentService
{
    public function startGatewayPayment(SmsPackagePurchase $purchase, array $urls): array
    {
        $gateway = $this->getPaymentGateway((string) $purchase->payment_method);

        if (! $gateway) {
            return ['success' => false, 'message' => 'Invalid payment gateway'];
        }

        $purchase->loadMissing(['package', 'tenant', 'user']);

        $tenant = $purchase->tenant ?? null;
        $user = $purchase->user ?? null;

        $customerName = $user?->name ?? 'Customer';
        $customerEmail = $user?->email ?? 'customer@example.com';
        $customerPhone = $user?->phone
            ?? $user?->profile?->phone
            ?? $tenant?->phone
            ?? '01700000000';

        if (! $purchase->transaction_id) {
            $purchase->transaction_id = (string) Str::uuid();
            $purchase->save();
        }

        $paymentData = [
            'amount' => (float) $purchase->total_price,
            'currency' => $purchase->currency ?? 'BDT',
            'transaction_id' => $purchase->transaction_id,
            'customer_name' => $customerName,
            'customer_email' => $customerEmail,
            'customer_phone' => $customerPhone,
            'customer_address' => $tenant?->address ?? 'N/A',
            'customer_city' => $tenant?->city ?? 'Dhaka',
            'customer_country' => 'Bangladesh',
            'product_name' => ($purchase->package?->name ?? 'SMS Package').' (SMS Credits)',
            'product_category' => 'SMS',
        ];

        if ($purchase->payment_method === 'sslcommerz') {
            $paymentData = array_merge($paymentData, [
                'success_url' => $urls['success_url'],
                'fail_url' => $urls['fail_url'],
                'cancel_url' => $urls['cancel_url'],
                'ipn_url' => $urls['ipn_url'] ?? null,
            ]);
        } else {
            // bKash
            $paymentData['callback_url'] = $urls['callback_url'];
        }

        $result = $gateway->initiatePayment($paymentData);

        if (! ($result['success'] ?? false)) {
            $purchase->update([
                'payment_status' => 'failed',
                'payment_details' => $result,
            ]);

            return $result;
        }

        $purchase->update([
            'payment_status' => 'processing',
            'payment_details' => $result,
        ]);

        return $result;
    }

    public function markFailed(SmsPackagePurchase $purchase, array $data = []): void
    {
        $purchase->update([
            'payment_status' => 'failed',
            'gateway_response' => $data ?: ($purchase->gateway_response ?? null),
        ]);
    }

    public function markCancelled(SmsPackagePurchase $purchase, array $data = []): void
    {
        $purchase->update([
            'payment_status' => 'cancelled',
            'gateway_response' => $data ?: ($purchase->gateway_response ?? null),
        ]);
    }

    public function verifyAndComplete(SmsPackagePurchase $purchase, string $gatewayTransactionId, array $callbackData = []): bool
    {
        $gateway = $this->getPaymentGateway((string) $purchase->payment_method);

        if (! $gateway) {
            return false;
        }

        $verification = $gateway->verifyPayment($gatewayTransactionId, $callbackData);

        Log::info('SMS Purchase Payment Verification Result', [
            'purchase_id' => $purchase->id,
            'gateway' => $purchase->payment_method,
            'gateway_transaction_id' => $gatewayTransactionId,
            'verification' => $verification,
        ]);

        $status = strtoupper(trim((string) ($verification['status'] ?? '')));
        $isSuccess = ($verification['success'] ?? false)
            && in_array($status, ['VALID', 'VALIDATED', 'COMPLETED'], true);

        if (! $isSuccess) {
            $purchase->update([
                'payment_status' => 'failed',
                'gateway_response' => $verification['response'] ?? $verification,
                'gateway_transaction_id' => $gatewayTransactionId,
            ]);

            return false;
        }

        $amount = (float) ($verification['amount'] ?? 0);
        if ($amount > 0 && abs($amount - (float) $purchase->total_price) > 0.01) {
            Log::warning('SMS Purchase amount mismatch', [
                'purchase_id' => $purchase->id,
                'expected' => (float) $purchase->total_price,
                'actual' => $amount,
                'gateway' => $purchase->payment_method,
            ]);

            $purchase->update([
                'payment_status' => 'failed',
                'gateway_response' => $verification['response'] ?? $verification,
                'gateway_transaction_id' => $gatewayTransactionId,
            ]);

            return false;
        }

        DB::transaction(function () use ($purchase, $verification, $gatewayTransactionId) {
            $purchase->refresh();

            $purchase->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
                'gateway_response' => $verification['response'] ?? $verification,
                'gateway_transaction_id' => $gatewayTransactionId,
                'payment_reference' => $purchase->payment_reference
                    ?? ($verification['trx_id'] ?? $verification['val_id'] ?? null),
            ]);
        });

        return true;
    }

    protected function getPaymentGateway(string $method)
    {
        $map = config('payment_gateways.gateways', []);
        $key = strtolower($method);

        if (! isset($map[$key])) {
            return null;
        }

        return app()->make($map[$key]);
    }
}
