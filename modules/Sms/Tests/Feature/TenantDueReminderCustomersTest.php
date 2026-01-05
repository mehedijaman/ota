<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Customer\Models\Customer;
use Modules\Customer\Models\CustomerContact;
use Modules\Tenant\Database\Factories\TenantFactory;
use Modules\User\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

it('shows only due customers with primary phone for the active tenant', function () {
    /** @var User $tenantUser */
    $tenantUser = User::factory()->create(['type' => 'tenant']);

    $tenant = TenantFactory::new()->create(['owner_id' => $tenantUser->id]);
    $tenant->members()->attach($tenantUser->id, [
        'is_owner' => true,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $otherTenant = TenantFactory::new()->create();

    $tenantUser->update(['active_tenant_id' => $tenant->id]);

    $dueOk = Customer::factory()->create([
        'tenant_id' => $tenant->id,
        'balance' => 150,
        'is_active' => true,
    ]);
    CustomerContact::factory()->primary()->create([
        'customer_id' => $dueOk->id,
        'phone' => '01700000000',
    ]);

    $noDue = Customer::factory()->create([
        'tenant_id' => $tenant->id,
        'balance' => 0,
        'is_active' => true,
    ]);
    CustomerContact::factory()->primary()->create([
        'customer_id' => $noDue->id,
        'phone' => '01700000001',
    ]);

    $missingPhone = Customer::factory()->create([
        'tenant_id' => $tenant->id,
        'balance' => 20,
        'is_active' => true,
    ]);
    CustomerContact::factory()->primary()->create([
        'customer_id' => $missingPhone->id,
        'phone' => '',
    ]);

    $otherTenantDue = Customer::factory()->create([
        'tenant_id' => $otherTenant->id,
        'balance' => 99,
        'is_active' => true,
    ]);
    CustomerContact::factory()->primary()->create([
        'customer_id' => $otherTenantDue->id,
        'phone' => '01700000002',
    ]);

    actingAs($tenantUser);

    $resp = get(route('tenant.sms.due.create'))
        ->assertOk();

    $resp->assertSee($dueOk->name);
    $resp->assertDontSee($noDue->name);
    $resp->assertDontSee($missingPhone->name);
    $resp->assertDontSee($otherTenantDue->name);
});

it('blocks sending due reminder to customers without due or outside tenant', function () {
    /** @var User $tenantUser */
    $tenantUser = User::factory()->create(['type' => 'tenant']);

    $tenant = TenantFactory::new()->create(['owner_id' => $tenantUser->id]);
    $tenant->members()->attach($tenantUser->id, [
        'is_owner' => true,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $otherTenant = TenantFactory::new()->create();

    $tenantUser->update(['active_tenant_id' => $tenant->id]);

    $noDue = Customer::factory()->create([
        'tenant_id' => $tenant->id,
        'balance' => 0,
        'is_active' => true,
    ]);

    $otherTenantDue = Customer::factory()->create([
        'tenant_id' => $otherTenant->id,
        'balance' => 50,
        'is_active' => true,
    ]);

    actingAs($tenantUser);

    post(route('tenant.sms.due.store'), [
        'customer_ids' => [$noDue->id, $otherTenantDue->id],
        'template' => 'Dear {name}, your due is {due}.',
    ])->assertSessionHasErrors('customer_ids.0');
});
