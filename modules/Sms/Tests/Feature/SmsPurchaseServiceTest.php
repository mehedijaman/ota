<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Sms\Models\SmsCreditLot;
use Modules\Sms\Models\SmsPackage;
use Modules\Sms\Services\SmsPurchaseService;
use Modules\Tenant\Database\Factories\TenantFactory;
use Modules\User\Models\User;

uses(RefreshDatabase::class);

it('approving multiple purchases accumulates credits and rejecting does not create lots', function () {
    $tenantUser = User::factory()->create(['type' => 'tenant']);
    $tenant = TenantFactory::new()->create(['owner_id' => $tenantUser->id]);
    $tenant->members()->attach($tenantUser->id, ['is_owner' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);

    $pkg = SmsPackage::create([
        'name' => 'Pack',
        'sms_credits' => 50,
        'duration_days' => 30,
        'price' => 10,
        'currency' => 'BDT',
        'is_active' => true,
    ]);

    $service = app(SmsPurchaseService::class);

    $p1 = $service->createTenantPurchase($tenant->id, $tenantUser->id, $pkg->id, 1);
    $p2 = $service->createTenantPurchase($tenant->id, $tenantUser->id, $pkg->id, 2);

    // Approve first purchase
    $service->approvePurchase($p1->id, $tenantUser->id, 'ok');

    expect(SmsCreditLot::query()->where('sms_package_purchase_id', $p1->id)->exists())->toBeTrue();

    // Reject second purchase
    $service->rejectPurchase($p2->id, $tenantUser->id, 'no');

    expect(SmsCreditLot::query()->where('sms_package_purchase_id', $p2->id)->exists())->toBeFalse();

    // Approve second by creating a fresh purchase and approving to test accumulation
    $p3 = $service->createTenantPurchase($tenant->id, $tenantUser->id, $pkg->id, 3);
    $service->approvePurchase($p3->id, $tenantUser->id, 'ok');

    $total = SmsCreditLot::query()->where('tenant_id', $tenant->id)->sum('credits_total');
    expect($total)->toBe(50 + (50 * 3));
});
