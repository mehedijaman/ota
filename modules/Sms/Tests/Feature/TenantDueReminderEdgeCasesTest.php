<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Customer\Models\Customer;
use Modules\Customer\Models\CustomerContact;
use Modules\Tenant\Database\Factories\TenantFactory;
use Modules\User\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

it('processes bulk due reminders and reports sent/failed counts', function () {
    $tenantUser = User::factory()->create(['type' => 'tenant']);
    $tenant = TenantFactory::new()->create(['owner_id' => $tenantUser->id]);
    $tenant->members()->attach($tenantUser->id, ['is_owner' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
    $tenantUser->update(['active_tenant_id' => $tenant->id]);

    $ok = Customer::factory()->create(['tenant_id' => $tenant->id, 'balance' => 10, 'is_active' => true]);
    CustomerContact::factory()->primary()->create(['customer_id' => $ok->id, 'phone' => '01700000001']);

    $fail = Customer::factory()->create(['tenant_id' => $tenant->id, 'balance' => 20, 'is_active' => true]);
    CustomerContact::factory()->primary()->create(['customer_id' => $fail->id, 'phone' => '01700000002']);

    actingAs($tenantUser);

    $fakeSender = new class extends \Modules\Sms\Services\SmsSendingService
    {
        public function __construct() {}

        public function sendNow(int $tenantId, ?int $userId, string $type, string $toPhone, string $message, array $meta = []): \Modules\Sms\Models\SmsMessage
        {
            $status = str_contains($toPhone, '00001') ? 'sent' : 'failed';

            $sms = \Modules\Sms\Models\SmsMessage::create([
                'tenant_id' => $tenantId,
                'user_id' => $userId,
                'type' => $type,
                'to_phone' => $toPhone,
                'message' => $message,
                'parts' => 1,
                'cost_credits' => 1,
                'status' => $status,
                'queued_at' => now(),
            ]);

            if ($status === 'failed') {
                $sms->error_message = 'Provider error';
                $sms->save();
            }

            return $sms->refresh();
        }
    };

    app()->instance(\Modules\Sms\Services\SmsSendingService::class, $fakeSender);

    $resp = post(route('tenant.sms.due.store'), [
        'customer_ids' => [$ok->id, $fail->id],
        'template' => 'Hello {name}, due {due}',
    ])->assertRedirect(route('tenant.sms.wallet'));

    $resp->assertSessionHas('success');
    $msg = session('success');
    expect(str_contains($msg, 'Sent: 1, Failed: 1'))->toBeTrue();
});

it('validates maximum number of customers for due reminder', function () {
    $tenantUser = User::factory()->create(['type' => 'tenant']);
    $tenant = TenantFactory::new()->create(['owner_id' => $tenantUser->id]);
    $tenant->members()->attach($tenantUser->id, ['is_owner' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
    $tenantUser->update(['active_tenant_id' => $tenant->id]);

    actingAs($tenantUser);

    $ids = range(1, 201);

    post(route('tenant.sms.due.store'), [
        'customer_ids' => $ids,
        'template' => 'Hi',
    ])->assertSessionHasErrors(['customer_ids']);
});
