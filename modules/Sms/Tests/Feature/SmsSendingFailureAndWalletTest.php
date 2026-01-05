<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Sms\Models\SmsCreditLot;
use Modules\Tenant\Database\Factories\TenantFactory;
use Modules\User\Models\User;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('marks sms as failed and releases reservations when gateway throws', function () {
    $tenantUser = User::factory()->create(['type' => 'tenant']);
    $tenant = TenantFactory::new()->create(['owner_id' => $tenantUser->id]);
    $tenant->members()->attach($tenantUser->id, ['is_owner' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
    $tenantUser->update(['active_tenant_id' => $tenant->id]);

    // Create credit lot
    SmsCreditLot::create([
        'tenant_id' => $tenant->id,
        'credits_total' => 10,
        'credits_used' => 0,
        'credits_reserved' => 0,
    ]);

    actingAs($tenantUser);

    // Bind gateway that throws
    app()->instance(Modules\Sms\Services\SmsGateway::class, new class extends Modules\Sms\Services\SmsGateway
    {
        public function send(string $phone, string $message): array
        {
            throw new RuntimeException('Network failure');
        }
    });

    $sending = app(Modules\Sms\Services\SmsSendingService::class);

    $sms = $sending->sendNow($tenant->id, $tenantUser->id, 'promotional', '01700000000', 'Hello');

    expect($sms->status)->toBe('failed');

    // reservations should be released => reserved credits 0
    $available = app(Modules\Sms\Services\SmsWalletService::class)->getAvailableCredits($tenant->id);
    expect($available)->toBe(10);
});

it('ignores expired credit lots when calculating available credits', function () {
    $tenantUser = User::factory()->create(['type' => 'tenant']);
    $tenant = TenantFactory::new()->create(['owner_id' => $tenantUser->id]);

    // expired lot
    SmsCreditLot::create([
        'tenant_id' => $tenant->id,
        'credits_total' => 5,
        'credits_used' => 0,
        'credits_reserved' => 0,
        'expires_at' => now()->subDay(),
    ]);

    // valid lot
    SmsCreditLot::create([
        'tenant_id' => $tenant->id,
        'credits_total' => 8,
        'credits_used' => 0,
        'credits_reserved' => 0,
    ]);

    $available = app(Modules\Sms\Services\SmsWalletService::class)->getAvailableCredits($tenant->id);

    expect($available)->toBe(8);
});
