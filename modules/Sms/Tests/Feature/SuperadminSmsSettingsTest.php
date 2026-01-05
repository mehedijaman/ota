<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Sms\Models\SmsSetting;
use Modules\Sms\Services\SmsGateway;
use Modules\User\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

uses(RefreshDatabase::class);

it('blocks tenant user from superadmin sms settings', function () {
    /** @var User $tenantUser */
    $tenantUser = User::factory()->create(['type' => 'tenant']);
    actingAs($tenantUser);

    get(route('superadmin.sms.settings.edit'))->assertForbidden();
});

it('superadmin can update sms provider settings and set default', function () {
    /** @var User $superadmin */
    $superadmin = User::factory()->create(['type' => 'user']);
    actingAs($superadmin);

    config()->set('sms.providers', [
        'FooProvider' => ['api_key' => null],
        'BarProvider' => ['username' => null, 'password' => null],
    ]);

    put(route('superadmin.sms.settings.update'), [
        'provider_class' => 'FooProvider',
        'credentials' => ['api_key' => 'X'],
        'is_default' => true,
        'is_active' => true,
    ])->assertRedirect(route('superadmin.sms.settings.edit'));

    expect(SmsSetting::query()->where('provider_class', 'FooProvider')->where('is_default', true)->exists())->toBeTrue();

    put(route('superadmin.sms.settings.update'), [
        'provider_class' => 'BarProvider',
        'credentials' => ['username' => 'u', 'password' => 'p'],
        'is_default' => true,
        'is_active' => true,
    ])->assertRedirect(route('superadmin.sms.settings.edit'));

    expect(SmsSetting::query()->where('provider_class', 'BarProvider')->where('is_default', true)->exists())->toBeTrue();
    expect(SmsSetting::query()->where('provider_class', 'FooProvider')->where('is_default', true)->exists())->toBeFalse();
});

it('superadmin can send test sms and sees friendly error on failure', function () {
    /** @var User $superadmin */
    $superadmin = User::factory()->create(['type' => 'user']);
    actingAs($superadmin);

    config()->set('sms.providers', [
        'FooProvider' => ['api_key' => null],
    ]);

    // anonymous fake gateway that records calls
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
        'provider_class' => 'FooProvider',
        'credentials' => ['api_key' => 'X'],
        'test_phone' => '01700000000',
        'test_message' => 'Hello',
    ])->assertRedirect()
        ->assertSessionHas('success', 'Test SMS sent.');

    expect($okGateway->calls)->toHaveCount(1);

    // anonymous failing gateway
    $failGateway = new class extends SmsGateway
    {
        public function sendWith(string $providerClass, array $credentials, string $phone, string $message): array
        {
            throw new RuntimeException('Bad credentials');
        }
    };

    app()->instance(SmsGateway::class, $failGateway);

    post(route('superadmin.sms.settings.test'), [
        'provider_class' => 'FooProvider',
        'credentials' => ['api_key' => 'bad'],
        'test_phone' => '01700000000',
        'test_message' => 'Hello',
    ])->assertRedirect()
        ->assertSessionHas('error');
});
