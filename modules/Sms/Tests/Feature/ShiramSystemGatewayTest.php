<?php

use Illuminate\Support\Facades\Http;
use Modules\Sms\Services\SmsGateway;
use Xenon\LaravelBDSms\Handler\RenderException;
use Xenon\LaravelBDSms\Provider\ShiramSystem;

it('sends ShiramSystem SMS via form POST', function () {
    Http::fake([
        'https://smsapi.shiramsystem.com/*' => Http::response('{"status":true,"error_code":0,"message":"Success"}', 200),
    ]);

    $gateway = app(SmsGateway::class);

    $result = $gateway->sendWith(
        ShiramSystem::class,
        [
            'endpoint' => 'https://smsapi.shiramsystem.com/user_api/',
            'email' => 'example@example.com',
            'password' => 'secret',
            'mask' => 'Non-Masking',
        ],
        '01711100000',
        'Test SMS'
    );

    Http::assertSent(function (\Illuminate\Http\Client\Request $request) {
        if ($request->url() !== 'https://smsapi.shiramsystem.com/user_api/') {
            return false;
        }

        if (strtoupper($request->method()) !== 'POST') {
            return false;
        }

        $data = $request->data();

        return ($data['email'] ?? null) === 'example@example.com'
            && ($data['password'] ?? null) === 'secret'
            && ($data['method'] ?? null) === 'send_sms'
            && ($data['mask'] ?? null) === 'Non-Masking'
            && ($data['message'] ?? null) === 'Test SMS'
            && is_array($data['mobile'] ?? null)
            && ($data['mobile'][0] ?? null) === '8801711100000';
    });

    expect($result)->toBeArray();
    expect($result)->toHaveKey('status');
});

it('throws a RenderException when ShiramSystem API returns an error', function () {
    Http::fake([
        'https://smsapi.shiramsystem.com/*' => Http::response('{"status":false,"error_code":111,"message":"SMS should be unicode"}', 200),
    ]);

    $gateway = app(SmsGateway::class);

    expect(fn () => $gateway->sendWith(
        ShiramSystem::class,
        [
            'endpoint' => 'https://smsapi.shiramsystem.com/user_api/',
            'email' => 'example@example.com',
            'password' => 'secret',
            'mask' => 'Non-Masking',
        ],
        '01711100000',
        'Test SMS'
    ))->toThrow(RenderException::class);
});
