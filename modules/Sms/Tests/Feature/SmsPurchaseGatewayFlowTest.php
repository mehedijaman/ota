<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Sms\Models\SmsCreditLot;
use Modules\Sms\Models\SmsPackage;
use Modules\Sms\Models\SmsPackagePurchase;
use Modules\Subscription\Services\Payment\PaymentGatewayInterface;
use Modules\Tenant\Database\Factories\TenantFactory;
use Modules\User\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

it('tenant can pay for sms package via sslcommerz and credits are auto-added after success callback', function () {
    $fakeSsl = makeFakeSslcommerzGateway();
    app()->instance('FakeSslcommerzGateway', $fakeSsl);
    config()->set('payment_gateways.gateways.sslcommerz', 'FakeSslcommerzGateway');

    /** @var User $tenantUser */
    $tenantUser = User::factory()->create(['type' => 'tenant']);

    $tenant = TenantFactory::new()->create(['owner_id' => $tenantUser->id]);

    $tenant->members()->attach($tenantUser->id, [
        'is_owner' => true,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $tenantUser->update(['active_tenant_id' => $tenant->id]);

    $package = SmsPackage::create([
        'name' => 'Starter SMS',
        'description' => 'Test',
        'sms_credits' => 100,
        'duration_days' => 30,
        'price' => 50,
        'currency' => 'BDT',
        'is_active' => true,
    ]);

    actingAs($tenantUser);

    $resp = post(route('tenant.sms.purchases.store'), [
        'sms_package_id' => $package->id,
        'quantity' => 1,
        'payment_method' => 'sslcommerz',
    ]);

    $resp->assertRedirect('https://example.test/sslcommerz');

    /** @var SmsPackagePurchase $purchase */
    $purchase = SmsPackagePurchase::query()->firstOrFail();

    expect($purchase->payment_status)->toBe('processing');

    // Simulate SSLCommerz success callback (no auth)
    post(route('sms.purchases.payments.success', $purchase->transaction_id), [
        'tran_id' => $purchase->transaction_id,
        'amount' => (string) $purchase->total_price,
        'currency' => $purchase->currency,
        'status' => 'VALID',
    ])->assertRedirect(route('tenant.sms.wallet'));

    $purchase->refresh();

    expect($purchase->payment_status)->toBe('paid');
    expect($purchase->status)->toBe('approved');

    expect(SmsCreditLot::query()->where('sms_package_purchase_id', $purchase->id)->exists())->toBeTrue();
});

it('tenant can pay for sms package via sslcommerz and credits are auto-added after ipn callback', function () {
    $fakeSsl = makeFakeSslcommerzGateway();
    app()->instance('FakeSslcommerzGateway', $fakeSsl);
    config()->set('payment_gateways.gateways.sslcommerz', 'FakeSslcommerzGateway');

    /** @var User $tenantUser */
    $tenantUser = User::factory()->create(['type' => 'tenant']);

    $tenant = TenantFactory::new()->create(['owner_id' => $tenantUser->id]);

    $tenant->members()->attach($tenantUser->id, [
        'is_owner' => true,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $tenantUser->update(['active_tenant_id' => $tenant->id]);

    $package = SmsPackage::create([
        'name' => 'Starter SMS',
        'description' => 'Test',
        'sms_credits' => 100,
        'duration_days' => 30,
        'price' => 50,
        'currency' => 'BDT',
        'is_active' => true,
    ]);

    actingAs($tenantUser);

    $resp = post(route('tenant.sms.purchases.store'), [
        'sms_package_id' => $package->id,
        'quantity' => 1,
        'payment_method' => 'sslcommerz',
    ]);

    $resp->assertRedirect('https://example.test/sslcommerz');

    /** @var SmsPackagePurchase $purchase */
    $purchase = SmsPackagePurchase::query()->firstOrFail();

    expect($purchase->payment_status)->toBe('processing');

    // Simulate SSLCommerz IPN callback (no auth)
    post(route('sms.purchases.payments.ipn', $purchase->transaction_id), [
        'tran_id' => $purchase->transaction_id,
        'val_id' => 'VAL123',
        'amount' => (string) $purchase->total_price,
        'currency' => $purchase->currency,
        'status' => 'VALID',
    ])->assertOk();

    $purchase->refresh();

    expect($purchase->payment_status)->toBe('paid');
    expect($purchase->status)->toBe('approved');

    expect(SmsCreditLot::query()->where('sms_package_purchase_id', $purchase->id)->exists())->toBeTrue();
});

it('tenant can pay for sms package via bkash and credits are auto-added after callback', function () {
    $fakeBkash = makeFakeBkashGateway();
    app()->instance('FakeBkashGateway', $fakeBkash);
    config()->set('payment_gateways.gateways.bkash', 'FakeBkashGateway');

    /** @var User $tenantUser */
    $tenantUser = User::factory()->create(['type' => 'tenant']);

    $tenant = TenantFactory::new()->create(['owner_id' => $tenantUser->id]);

    $tenant->members()->attach($tenantUser->id, [
        'is_owner' => true,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $tenantUser->update(['active_tenant_id' => $tenant->id]);

    $package = SmsPackage::create([
        'name' => 'Starter SMS',
        'description' => 'Test',
        'sms_credits' => 100,
        'duration_days' => 30,
        'price' => 50,
        'currency' => 'BDT',
        'is_active' => true,
    ]);

    actingAs($tenantUser);

    $resp = post(route('tenant.sms.purchases.store'), [
        'sms_package_id' => $package->id,
        'quantity' => 1,
        'payment_method' => 'bkash',
    ]);

    $resp->assertRedirect('https://example.test/bkash');

    /** @var SmsPackagePurchase $purchase */
    $purchase = SmsPackagePurchase::query()->firstOrFail();

    expect($purchase->payment_status)->toBe('processing');

    // Simulate bKash callback (no auth)
    post(route('sms.purchases.payments.callback', $purchase->transaction_id), [
        'paymentID' => 'PAY123',
        'status' => 'success',
        'amount' => (string) $purchase->total_price,
        'currency' => $purchase->currency,
    ])->assertRedirect(route('tenant.sms.wallet'));

    $purchase->refresh();

    expect($purchase->payment_status)->toBe('paid');
    expect($purchase->status)->toBe('approved');

    expect(SmsCreditLot::query()->where('sms_package_purchase_id', $purchase->id)->exists())->toBeTrue();
});

function makeFakeSslcommerzGateway()
{
    return new class implements PaymentGatewayInterface
    {
        public function initiatePayment(array $data): array
        {
            return [
                'success' => true,
                'gateway_url' => 'https://example.test/sslcommerz',
                'response' => $data,
            ];
        }

        public function verifyPayment(string $transactionId, array $callbackData = []): array
        {
            return [
                'success' => true,
                'status' => 'VALID',
                'tran_id' => $transactionId,
                'amount' => $callbackData['amount'] ?? '0',
                'currency' => $callbackData['currency'] ?? 'BDT',
                'response' => $callbackData,
            ];
        }

        public function refundPayment(string $transactionId, float $amount): array
        {
            return ['success' => false];
        }

        public function getPaymentStatus(string $transactionId): string
        {
            return 'unknown';
        }
    };
}

function makeFakeBkashGateway()
{
    return new class implements PaymentGatewayInterface
    {
        public function initiatePayment(array $data): array
        {
            return [
                'success' => true,
                'bkash_url' => 'https://example.test/bkash',
                'response' => $data,
            ];
        }

        public function verifyPayment(string $transactionId, array $callbackData = []): array
        {
            return [
                'success' => true,
                'status' => 'Completed',
                'trx_id' => $transactionId,
                'amount' => $callbackData['amount'] ?? '0',
                'currency' => $callbackData['currency'] ?? 'BDT',
                'response' => $callbackData,
            ];
        }

        public function refundPayment(string $transactionId, float $amount): array
        {
            return ['success' => false];
        }

        public function getPaymentStatus(string $transactionId): string
        {
            return 'unknown';
        }
    };
}
