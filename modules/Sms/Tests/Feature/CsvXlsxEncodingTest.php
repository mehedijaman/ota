<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Sms\Models\SmsTemplate;
use Modules\Sms\Services\SmsGateway;
use Modules\User\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

it('parses csv with utf8 bom and extracts phone correctly', function () {
    $superadmin = User::factory()->create(['type' => 'user']);
    actingAs($superadmin);

    $template = SmsTemplate::create(['name' => 'Enc', 'content' => 'Hi', 'is_active' => true]);

    $bom = "\xEF\xBB\xBF"; // UTF-8 BOM
    $csv = $bom."01711110000\n";

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
            $this->calls[] = $phone;

            return ['status' => 'sent'];
        }
    };

    app()->instance(SmsGateway::class, $fake);

    $resp = post(route('superadmin.sms.send.store'), [
        'template_id' => $template->id,
        'numbers_file' => UploadedFile::fake()->createWithContent('numbers.csv', $csv),
    ])->assertRedirect();

    expect($fake->calls[0])->toBe('01711110000');
});

it('handles xlsx files by using Excel::toArray and extracts phones', function () {
    $superadmin = User::factory()->create(['type' => 'user']);
    actingAs($superadmin);

    $template = SmsTemplate::create(['name' => 'X', 'content' => 'Hi', 'is_active' => true]);

    // Mock Excel::toArray to simulate parsed sheets
    Excel::shouldReceive('toArray')->once()->andReturn([
        [
            ['01722220000'],
            ['01733330000'],
        ],
    ]);

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
            $this->calls[] = $phone;

            return ['status' => 'sent'];
        }
    };

    app()->instance(SmsGateway::class, $fake);

    // Provide a fake xlsx file (content not used because Excel is mocked)
    $resp = post(route('superadmin.sms.send.store'), [
        'template_id' => $template->id,
        'numbers_file' => UploadedFile::fake()->createWithContent('numbers.xlsx', 'dummy'),
    ])->assertRedirect();

    expect($fake->calls)->toContain('01722220000');
    expect($fake->calls)->toContain('01733330000');
});
