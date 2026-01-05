<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Modules\Sms\Models\SmsSetting;
use Modules\Sms\Models\SmsTemplate;
use Modules\User\Models\User;
use Xenon\LaravelBDSms\Provider\ShiramSystem;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

uses(RefreshDatabase::class);

it('superadmin can CRUD sms templates', function () {
    /** @var User $superadmin */
    $superadmin = User::factory()->create(['type' => 'user']);
    actingAs($superadmin);

    post(route('superadmin.sms.templates.store'), [
        'name' => 'Promo A',
        'content' => 'Hello from template',
        'is_active' => true,
    ])->assertRedirect(route('superadmin.sms.templates.index'));

    /** @var SmsTemplate $template */
    $template = SmsTemplate::query()->firstOrFail();
    expect($template->name)->toBe('Promo A');

    put(route('superadmin.sms.templates.update', $template->id), [
        'name' => 'Promo B',
        'content' => 'Updated',
        'is_active' => false,
    ])->assertRedirect(route('superadmin.sms.templates.index'));

    $template->refresh();
    expect($template->name)->toBe('Promo B');
    expect((bool) $template->is_active)->toBeFalse();

    delete(route('superadmin.sms.templates.destroy', $template->id))
        ->assertRedirect(route('superadmin.sms.templates.index'));

    expect(SmsTemplate::query()->count())->toBe(0);
});

it('superadmin can send promotional SMS using template and csv import', function () {
    /** @var User $superadmin */
    $superadmin = User::factory()->create(['type' => 'user']);
    actingAs($superadmin);

    SmsSetting::query()->create([
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

    $template = SmsTemplate::query()->create([
        'name' => 'T1',
        'content' => 'Test message',
        'is_active' => true,
    ]);

    Http::fake([
        'https://smsapi.shiramsystem.com/*' => Http::response('{"status":true,"error_code":0,"message":"Success"}', 200),
    ]);

    $csv = "01711100000\n01822200000\n";

    $resp = post(route('superadmin.sms.send.store'), [
        'template_id' => $template->id,
        'numbers_file' => UploadedFile::fake()->createWithContent('numbers.csv', $csv),
    ]);

    $resp->assertRedirect();
    $resp->assertSessionHas('success');
});

it('validates required fields when creating template', function () {
    $superadmin = User::factory()->create(['type' => 'user']);
    actingAs($superadmin);

    post(route('superadmin.sms.templates.store'), [
        // missing name and content
    ])->assertSessionHasErrors(['name', 'content']);
});

it('blocks duplicate template names', function () {
    $superadmin = User::factory()->create(['type' => 'user']);
    actingAs($superadmin);

    SmsTemplate::query()->create([
        'name' => 'UniqueName',
        'content' => 'abc',
        'is_active' => true,
    ]);

    post(route('superadmin.sms.templates.store'), [
        'name' => 'UniqueName',
        'content' => 'def',
        'is_active' => true,
    ])->assertSessionHasErrors(['name']);
});

it('validates template content is not empty or too long', function () {
    $superadmin = User::factory()->create(['type' => 'user']);
    actingAs($superadmin);

    post(route('superadmin.sms.templates.store'), [
        'name' => 'T2',
        'content' => '',
        'is_active' => true,
    ])->assertSessionHasErrors(['content']);

    $long = str_repeat('A', 2001);
    post(route('superadmin.sms.templates.store'), [
        'name' => 'T3',
        'content' => $long,
        'is_active' => true,
    ])->assertSessionHasErrors(['content']);
});
