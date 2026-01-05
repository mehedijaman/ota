<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Modules\Sms\Models\SmsTemplate;
use Modules\Sms\Services\SmsGateway;
use Modules\User\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

it('handles very large csv sends and deduplicates phones', function () {
    $superadmin = User::factory()->create(['type' => 'user']);
    actingAs($superadmin);

    $template = SmsTemplate::create(['name' => 'Large', 'content' => 'Hi', 'is_active' => true]);

    // create 1000 numbers with duplicates
    $phones = [];
    for ($i = 0; $i < 1000; $i++) {
        $n = '017'.str_pad((string) ($i % 500), 8, '0', STR_PAD_LEFT);
        $phones[] = $n;
    }

    $csv = implode("\n", $phones)."\n";

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
    ]);

    $resp->assertRedirect();

    // should dedupe to 500 unique numbers
    expect(count($fake->calls))->toBe(500);
});
