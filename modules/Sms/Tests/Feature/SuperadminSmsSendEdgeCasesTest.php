<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Modules\Sms\Models\SmsTemplate;
use Modules\Sms\Services\SmsGateway;
use Modules\User\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

it('parses csv with empty lines and ignores invalid phones and deduplicates', function () {
    $superadmin = User::factory()->create(['type' => 'user']);
    actingAs($superadmin);

    $template = SmsTemplate::create(['name' => 'T', 'content' => 'Hi', 'is_active' => true]);

    $csv = "\n01711111111\ninvalid\n01711111111\n\n";

    $calls = [];
    $fake = new class($calls) extends SmsGateway
    {
        public array $calls = [];

        public function __construct(&$calls)
        {
            $this->calls = &$calls;
        }

        public function send(string $phone, string $message): array
        {
            $this->calls[] = [$phone, $message];

            return ['status' => 'sent'];
        }
    };

    app()->instance(SmsGateway::class, $fake);

    $resp = post(route('superadmin.sms.send.store'), [
        'template_id' => $template->id,
        'numbers_file' => UploadedFile::fake()->createWithContent('numbers.csv', $csv),
    ]);

    $resp->assertRedirect();
    expect(count($fake->calls))->toBe(1);
    expect($fake->calls[0][0])->toBe('01711111111');
});

it('summarizes partial failures from gateway exceptions', function () {
    $superadmin = User::factory()->create(['type' => 'user']);
    actingAs($superadmin);

    $template = SmsTemplate::create(['name' => 'T2', 'content' => 'Hi', 'is_active' => true]);

    $fake = new class extends SmsGateway
    {
        public function send(string $phone, string $message): array
        {
            if (str_ends_with($phone, '2')) {
                throw new RuntimeException('Bad');
            }

            return ['status' => 'sent'];
        }
    };

    app()->instance(SmsGateway::class, $fake);

    $resp = post(route('superadmin.sms.send.store'), [
        'template_id' => $template->id,
        'to_phones' => '01711111111,01711111112,invalid',
    ])->assertRedirect();

    $resp->assertSessionHas('success');
    $msg = session('success');
    expect(str_contains($msg, 'Sent: 1, Failed: 1'))->toBeTrue();
});
