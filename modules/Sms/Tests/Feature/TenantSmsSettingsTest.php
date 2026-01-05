<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Sms\Models\SmsTenantSetting;
use Modules\Tenant\Database\Factories\TenantFactory;
use Modules\User\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\put;

uses(RefreshDatabase::class);

it('tenant can view sms settings and defaults', function () {
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

    actingAs($tenantUser);

    $resp = get(route('tenant.sms.settings.edit'))->assertOk();
    $resp->assertSee('due_reminder_template');
    $resp->assertSee('Dear');

    expect(SmsTenantSetting::query()->where('tenant_id', $tenant->id)->exists())->toBeTrue();
});

it('tenant can update sms settings', function () {
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

    actingAs($tenantUser);

    put(route('tenant.sms.settings.update'), [
        'due_reminder_sms_enabled' => true,
        'due_reminder_template' => 'Hello {name}, due {due}.',
        'sale_sms_enabled' => true,
        'sale_sms_template' => 'Sale template',
    ])->assertRedirect(route('tenant.sms.settings.edit'));

    $settings = SmsTenantSetting::query()->where('tenant_id', $tenant->id)->firstOrFail();

    expect((bool) $settings->due_reminder_sms_enabled)->toBeTrue();
    expect((string) $settings->due_reminder_template)->toBe('Hello {name}, due {due}.');
    expect((bool) $settings->sale_sms_enabled)->toBeTrue();
    expect((string) $settings->sale_sms_template)->toBe('Sale template');
});
