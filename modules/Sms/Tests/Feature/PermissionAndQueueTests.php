<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Models\User;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('redirects unauthenticated user to login for superadmin routes', function () {
    get(route('superadmin.sms.templates.index'))->assertRedirect();
});

it('forbids tenant user from superadmin settings', function () {
    $tenant = User::factory()->create(['type' => 'tenant']);
    actingAs($tenant = $tenant);

    get(route('superadmin.sms.settings.edit'))->assertForbidden();
});
