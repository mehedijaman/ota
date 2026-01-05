<?php

use Illuminate\Support\Str;
use Modules\Customer\Models\Customer;
use Modules\Customer\Models\CustomerContact;
use Modules\Sms\Models\SmsCreditLot;
use Modules\Sms\Models\SmsPackage;
use Modules\Sms\Models\SmsPackagePurchase;
use Modules\Sms\Services\SmsGateway;
use Modules\Sms\Services\SmsSendingService;
use Modules\Sms\Services\SmsWalletService;
use Modules\Tenant\Database\Factories\TenantFactory;
use Modules\User\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

it('tenant can request a package and superadmin can approve it to add credits', function () {
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

    post(route('tenant.sms.purchases.store'), [
        'sms_package_id' => $package->id,
        'quantity' => 2,
        'payment_method' => 'manual',
        'payment_reference' => 'TXN-123',
    ])->assertRedirect();

    /** @var SmsPackagePurchase $purchase */
    $purchase = SmsPackagePurchase::query()->firstOrFail();

    expect($purchase->status)->toBe('pending');
    expect((int) $purchase->total_credits)->toBe(200);

    /** @var User $superadmin */
    $superadmin = User::factory()->create(['type' => 'user']);

    actingAs($superadmin);

    post(route('superadmin.sms.purchases.approve', $purchase->id), [
        'admin_notes' => 'Approved',
    ])->assertRedirect();

    $purchase->refresh();

    expect($purchase->status)->toBe('approved');
    expect($purchase->approved_by)->toBe($superadmin->id);

    expect(SmsCreditLot::query()->where('sms_package_purchase_id', $purchase->id)->exists())->toBeTrue();
});

it('sending SMS consumes credits on success', function () {
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
        'sms_credits' => 10,
        'duration_days' => 30,
        'price' => 0,
        'currency' => 'BDT',
        'is_active' => true,
    ]);

    $purchase = SmsPackagePurchase::create([
        'tenant_id' => $tenant->id,
        'user_id' => $tenantUser->id,
        'sms_package_id' => $package->id,
        'quantity' => 1,
        'total_credits' => 10,
        'total_price' => 0,
        'currency' => 'BDT',
        'status' => 'approved',
        'starts_at' => now(),
        'expires_at' => now()->addDays(30),
        'approved_by' => $tenantUser->id,
        'approved_at' => now(),
    ]);

    SmsCreditLot::create([
        'tenant_id' => $tenant->id,
        'sms_package_purchase_id' => $purchase->id,
        'credits_total' => 10,
        'credits_used' => 0,
        'credits_reserved' => 0,
        'expires_at' => now()->addDays(30),
    ]);

    app()->instance(SmsGateway::class, new class extends SmsGateway
    {
        public function send(string $phone, string $message): array
        {
            return ['status' => true, 'message_id' => (string) Str::uuid()];
        }
    });

    $wallet = app(SmsWalletService::class);
    expect($wallet->getAvailableCredits($tenant->id))->toBe(10);

    /** @var SmsSendingService $sending */
    $sending = app(SmsSendingService::class);

    $sms = $sending->sendNow(
        tenantId: $tenant->id,
        userId: $tenantUser->id,
        type: 'promotional',
        toPhone: '01700000000',
        message: 'Hello',
        meta: []
    );

    expect($sms->status)->toBe('sent');

    $lot = SmsCreditLot::query()->where('tenant_id', $tenant->id)->firstOrFail();
    expect((int) $lot->credits_used)->toBe(1);
    expect($wallet->getAvailableCredits($tenant->id))->toBe(9);
});

it('shows an error (not 500) when SMS balance is insufficient', function () {
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

    $customer = Customer::factory()->create([
        'tenant_id' => $tenant->id,
        'balance' => 100,
        'is_active' => true,
    ]);

    CustomerContact::factory()->primary()->create([
        'customer_id' => $customer->id,
        'phone' => '01700000000',
    ]);

    actingAs($tenantUser);

    post(route('tenant.sms.due.store'), [
        'customer_ids' => [$customer->id],
        'template' => 'Dear {name}, your due is {due}.',
    ])->assertRedirect()
        ->assertSessionHas('error', 'Insufficient SMS balance.');
});
