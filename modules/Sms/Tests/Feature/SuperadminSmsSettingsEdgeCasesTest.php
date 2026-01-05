<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Sms\Models\SmsSetting;
use Modules\Sms\Services\SmsGateway;
use Modules\User\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

uses(RefreshDatabase::class);

it('rejects invalid provider_class when updating settings', function () {
    $superadmin = User::factory()->create(['type' => 'user']);
    actingAs($superadmin);

    config()->set('sms.providers', [
        'A' => ['k' => null],
    ]);

    put(route('superadmin.sms.settings.update'), [
        'provider_class' => 'NonExist',
        'credentials' => [],
    ])->assertSessionHasErrors(['provider_class']);
});

it('requires test_phone when sending test sms', function () {
    $superadmin = User::factory()->create(['type' => 'user']);
    actingAs($superadmin);

    config()->set('sms.providers', [
        'Foo' => ['k' => null],
    ]);

    post(route('superadmin.sms.settings.test'), [
        'provider_class' => 'Foo',
        'credentials' => [],
        // missing test_phone
    ])->assertSessionHasErrors(['test_phone']);
});

it('uses default message when test_message is empty', function () {
    $superadmin = User::factory()->create(['type' => 'user']);
    actingAs($superadmin);

    config()->set('sms.providers', [
        'Foo' => ['k' => null],
    ]);

    $okGateway = new class extends SmsGateway
    {
        public array $calls = [];

        public function sendWith(string $providerClass, array $credentials, string $phone, string $message): array
        {
            $this->calls[] = compact('providerClass', 'credentials', 'phone', 'message');

            return ['status' => 'sent'];
        }
    };

    app()->instance(SmsGateway::class, $okGateway);

    post(route('superadmin.sms.settings.test'), [
        'provider_class' => 'Foo',
        'credentials' => [],
        'test_phone' => '01700000000',
        'test_message' => '',
    ])->assertRedirect()
        ->assertSessionHas('success');

    expect($okGateway->calls)->toHaveCount(1);
    expect($okGateway->calls[0]['message'])->toBe('Test SMS from SlimPOS.');
});

it('prefers an active provider when default is inactive', function () {
    $superadmin = User::factory()->create(['type' => 'user']);
    actingAs($superadmin);

    // Create settings: default but inactive, and another active provider
    SmsSetting::create([
        'provider_class' => 'P\Default',
        'credentials' => [],
        'is_default' => true,
        'is_active' => false,
    ]);

    SmsSetting::create([
        'provider_class' => 'P\Active',
        'credentials' => [],
        'is_default' => false,
        'is_active' => true,
    ]);

    $gateway = new class extends SmsGateway
    {
        public function sendWith(string $providerClass, array $credentials, string $phone, string $message): array
        {
            return ['used' => $providerClass];
        }
    };

    app()->instance(SmsGateway::class, $gateway);

    $res = app(SmsGateway::class)->send('01700000000', 'hi');

    expect($res['used'])->toBe('P\\Active');
});
