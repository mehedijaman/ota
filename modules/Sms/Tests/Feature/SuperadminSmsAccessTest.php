<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Sms\Models\SmsTemplate;
use Modules\User\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('tenant user cannot access superadmin sms send page', function () {
    /** @var User $tenantUser */
    $tenantUser = User::factory()->create(['type' => 'tenant']);
    actingAs($tenantUser);

    get(route('superadmin.sms.send.create'))->assertForbidden();
});

it('superadmin can access sms send page and only sees active templates', function () {
    /** @var User $superadmin */
    $superadmin = User::factory()->create(['type' => 'user']);
    actingAs($superadmin);

    $active = SmsTemplate::create([
        'name' => 'Active',
        'content' => 'Hello',
        'is_active' => true,
    ]);

    $inactive = SmsTemplate::create([
        'name' => 'Inactive',
        'content' => 'Hidden',
        'is_active' => false,
    ]);

    $resp = get(route('superadmin.sms.send.create'))->assertOk();

    $resp->assertSee($active->name);
    $resp->assertDontSee($inactive->name);
});
