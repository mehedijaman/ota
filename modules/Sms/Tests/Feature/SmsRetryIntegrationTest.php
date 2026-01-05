<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Sms\Models\SmsCreditLot;
use Modules\Tenant\Database\Factories\TenantFactory;
use Modules\User\Models\User;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('retries transient failures and eventually marks sms as sent and commits credits', function () {
    $tenantUser = User::factory()->create(['type' => 'tenant']);
    $tenant = TenantFactory::new()->create(['owner_id' => $tenantUser->id]);
    $tenant->members()->attach($tenantUser->id, ['is_owner' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
    $tenantUser->update(['active_tenant_id' => $tenant->id]);

    // Create credit lot
    SmsCreditLot::create([
        'tenant_id' => $tenant->id,
        'credits_total' => 10,
        'credits_used' => 0,
        'credits_reserved' => 0,
    ]);

    actingAs($tenantUser);

    $state = (object) ['attempts' => 0];

    // Ensure there is an active sms provider setting so SmsGateway::send() proceeds
    \Modules\Sms\Models\SmsSetting::create([
        'provider_class' => \Xenon\LaravelBDSms\Provider\ShiramSystem::class,
        'credentials' => [],
        'is_active' => true,
        'is_default' => true,
    ]);

    // Bind gateway that uses a fake Sender which fails twice then succeeds
    app()->instance(Modules\Sms\Services\SmsGateway::class, new class($state) extends Modules\Sms\Services\SmsGateway
    {
        public $state;

        public function __construct($state)
        {
            $this->state = $state;
        }

        protected function getSenderInstance()
        {
            $state = $this->state;

            return new class($state)
            {
                public $state;

                public function __construct($state)
                {
                    $this->state = $state;
                }

                public function setProvider($p) {}

                public function setMobile($m) {}

                public function setMessage($m) {}

                public function setQueue($b) {}

                public function setConfig($c) {}

                public function send()
                {
                    $this->state->attempts++;
                    if ($this->state->attempts <= 2) {
                        throw new RuntimeException('Transient failure');
                    }

                    return ['status' => 'sent'];
                }
            };
        }
    });

    $sending = app(Modules\Sms\Services\SmsSendingService::class);

    $sms = $sending->sendNow($tenant->id, $tenantUser->id, 'promotional', '01700000000', 'Hello');

    // inspect how many attempts the fake sender recorded (debugging assertion)
    expect($state->attempts)->toBe(3);

    expect($sms->status)->toBe('sent');

    // reservations should be committed => available credits reduced to 9 (one credit consumed)
    $available = app(Modules\Sms\Services\SmsWalletService::class)->getAvailableCredits($tenant->id);
    expect($available)->toBe(9);
});

it('gives up after max retries and releases reservations on permanent failure', function () {
    $tenantUser = User::factory()->create(['type' => 'tenant']);
    $tenant = TenantFactory::new()->create(['owner_id' => $tenantUser->id]);
    $tenant->members()->attach($tenantUser->id, ['is_owner' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
    $tenantUser->update(['active_tenant_id' => $tenant->id]);

    // Create credit lot
    SmsCreditLot::create([
        'tenant_id' => $tenant->id,
        'credits_total' => 10,
        'credits_used' => 0,
        'credits_reserved' => 0,
    ]);

    actingAs($tenantUser);

    // Bind gateway that uses a fake Sender which always throws
    app()->instance(Modules\Sms\Services\SmsGateway::class, new class extends Modules\Sms\Services\SmsGateway
    {
        protected function getSenderInstance()
        {
            return new class
            {
                public function setProvider($p) {}

                public function setMobile($m) {}

                public function setMessage($m) {}

                public function setQueue($b) {}

                public function setConfig($c) {}

                public function send()
                {
                    throw new RuntimeException('Permanent failure');
                }
            };
        }
    });

    $sending = app(Modules\Sms\Services\SmsSendingService::class);

    $sms = $sending->sendNow($tenant->id, $tenantUser->id, 'promotional', '01700000000', 'Hello');

    expect($sms->status)->toBe('failed');

    // reservations should be released => reserved credits 0 and available back to 10
    $available = app(Modules\Sms\Services\SmsWalletService::class)->getAvailableCredits($tenant->id);
    expect($available)->toBe(10);
});
