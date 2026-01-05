<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Modules\Sms\Models\SmsSetting;
use Xenon\LaravelBDSms\Provider\ShiramSystem;

use function Pest\Laravel\postJson;

it('returns otp in test mode', function () {
    Config::set('sms.otp.test_mode', true);

    $response = postJson(route('sms.register.otp.send'), [
        'phone' => '01711100000',
    ]);

    $response->assertOk();
    $response->assertJson([
        'sent' => true,
        'test_mode' => true,
    ]);

    expect($response->json('otp'))->not->toBeNull();
});

it('sends otp via gateway when test mode is false', function () {
    Config::set('sms.otp.test_mode', false);

    SmsSetting::query()->delete();
    SmsSetting::create([
        'provider_class' => ShiramSystem::class,
        'credentials' => [
            'endpoint' => 'https://smsapi.shiramsystem.com/user_api/',
            'email' => 'example@example.com',
            'password' => 'secret',
            'mask' => 'Non-Masking',
        ],
        'is_default' => true,
        'is_active' => true,
    ]);

    Http::fake([
        'https://smsapi.shiramsystem.com/*' => Http::response('{"status":true,"error_code":0,"message":"Success"}', 200),
    ]);

    $response = postJson(route('sms.register.otp.send'), [
        'phone' => '01711100000',
    ]);

    $response->assertOk();
    $response->assertJson([
        'sent' => true,
    ]);
    expect($response->json('otp'))->toBeNull();

    Http::assertSent(fn (\Illuminate\Http\Client\Request $request) => $request->url() === 'https://smsapi.shiramsystem.com/user_api/');
});

it('enforces rate limit for OTP requests', function () {
    // Use test mode so controller returns OTP without requiring provider
    config()->set('sms.otp.test_mode', true);

    $phone = '01711100000';

    $first = postJson(route('sms.register.otp.send'), ['phone' => $phone]);
    $first->assertStatus(200);

    $second = postJson(route('sms.register.otp.send'), ['phone' => $phone]);
    $second->assertStatus(429);
    $second->assertJsonStructure(['retry_after']);
});

it('verifies otp and enforces attempts limit', function () {
    config()->set('sms.otp.test_mode', true);

    $response = postJson(route('sms.register.otp.send'), ['phone' => '01722200000']);
    $otp = $response->json('otp');

    // correct verify
    $v = postJson(route('sms.register.otp.verify'), ['phone' => '01722200000', 'otp' => $otp]);
    $v->assertStatus(200);
    $v->assertJson(['verified' => true]);

    // create a record and simulate wrong attempts (use test mode)
    config()->set('sms.otp.test_mode', true);
    $resp = postJson(route('sms.register.otp.send'), ['phone' => '01733300000']);
    $resp->assertStatus(200);

    for ($i = 0; $i < 6; $i++) {
        $r = postJson(route('sms.register.otp.verify'), ['phone' => '01733300000', 'otp' => '000000']);
        if ($i < 5) {
            $r->assertStatus(422);
        } else {
            $r->assertStatus(429);
        }
    }
});
