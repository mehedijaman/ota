<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Agent\Models\Agent;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Role::create(['name' => 'superadmin']);
    $this->user->assignRole('superadmin');

    $this->loggedRequest = $this->actingAs($this->user);

    $this->agent = Agent::factory()->approved()->create();
});

test('agent list can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/agents');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Agent/AgentIndex')
            ->has('agents.data', 1)
    );
});

test('agent create page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/agents/create');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Agent/AgentForm')
    );
});

test('agent can be created', function () {
    $response = $this->loggedRequest->post('/admin/agents', [
        'company_name' => 'Test Company',
        'contact_person' => 'John Doe',
        'email' => 'test@company.com',
        'phone' => '1234567890',
        'address' => '123 Test Street',
        'city' => 'Dhaka',
        'country' => 'Bangladesh',
        'status' => 'approved',
    ]);

    $response->assertRedirect('/admin/agents');

    $this->assertDatabaseHas('agents', [
        'company_name' => 'Test Company',
        'email' => 'test@company.com',
    ]);
});

test('agent show page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/agents/'.$this->agent->uuid);

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Agent/AgentShow')
            ->has('agent')
    );
});

test('agent edit page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/agents/'.$this->agent->uuid.'/edit');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Agent/AgentForm')
            ->has('agent')
    );
});

test('agent can be updated', function () {
    $response = $this->loggedRequest->put('/admin/agents/'.$this->agent->uuid, [
        'company_name' => 'Updated Company',
        'contact_person' => 'Jane Doe',
        'email' => $this->agent->email,
        'status' => 'suspended',
    ]);

    $response->assertRedirect('/admin/agents');

    $this->assertDatabaseHas('agents', [
        'id' => $this->agent->id,
        'company_name' => 'Updated Company',
        'contact_person' => 'Jane Doe',
        'status' => 'suspended',
    ]);
});

test('agent can be deleted', function () {
    $response = $this->loggedRequest->delete('/admin/agents/'.$this->agent->uuid);

    $response->assertRedirect('/admin/agents');

    $this->assertSoftDeleted('agents', [
        'id' => $this->agent->id,
    ]);
});

test('agent recycle bin can be rendered', function () {
    $this->agent->delete();

    $response = $this->loggedRequest->get('/admin/agents/recycle-bin');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Agent/AgentRecycleBin')
            ->has('agents.data', 1)
    );
});

test('agent can be restored from recycle bin', function () {
    $this->agent->delete();

    $response = $this->loggedRequest->post('/admin/agents/recycle-bin/'.$this->agent->id.'/restore');

    $response->assertRedirect('/admin/agents/recycle-bin');

    $this->assertDatabaseHas('agents', [
        'id' => $this->agent->id,
        'deleted_at' => null,
    ]);
});

test('agent can be permanently deleted from recycle bin', function () {
    $this->agent->delete();

    $response = $this->loggedRequest->delete('/admin/agents/recycle-bin/'.$this->agent->id.'/force');

    $response->assertRedirect('/admin/agents/recycle-bin');

    $this->assertDatabaseMissing('agents', [
        'id' => $this->agent->id,
    ]);
});

test('agent recycle bin can be emptied', function () {
    $this->agent->delete();

    $response = $this->loggedRequest->delete('/admin/agents/recycle-bin/empty');

    $response->assertRedirect('/admin/agents/recycle-bin');

    $this->assertDatabaseMissing('agents', [
        'id' => $this->agent->id,
    ]);
});

test('all agents in recycle bin can be restored', function () {
    $agent2 = Agent::factory()->create();
    $this->agent->delete();
    $agent2->delete();

    $response = $this->loggedRequest->post('/admin/agents/recycle-bin/restore-all');

    $response->assertRedirect('/admin/agents/recycle-bin');

    $this->assertDatabaseHas('agents', [
        'id' => $this->agent->id,
        'deleted_at' => null,
    ]);

    $this->assertDatabaseHas('agents', [
        'id' => $agent2->id,
        'deleted_at' => null,
    ]);
});

test('agent validation requires company name', function () {
    $response = $this->loggedRequest->post('/admin/agents', [
        'company_name' => '',
        'contact_person' => 'John Doe',
        'email' => 'test@example.com',
    ]);

    $response->assertSessionHasErrors(['company_name']);
});

test('agent validation requires contact person', function () {
    $response = $this->loggedRequest->post('/admin/agents', [
        'company_name' => 'Test Company',
        'contact_person' => '',
        'email' => 'test@example.com',
    ]);

    $response->assertSessionHasErrors(['contact_person']);
});

test('agent validation requires email', function () {
    $response = $this->loggedRequest->post('/admin/agents', [
        'company_name' => 'Test Company',
        'contact_person' => 'John Doe',
        'email' => '',
    ]);

    $response->assertSessionHasErrors(['email']);
});

test('agent email must be unique', function () {
    $response = $this->loggedRequest->post('/admin/agents', [
        'company_name' => 'Another Company',
        'contact_person' => 'Jane Doe',
        'email' => $this->agent->email,
    ]);

    $response->assertSessionHasErrors(['email']);
});

test('agent status must be valid', function () {
    $response = $this->loggedRequest->post('/admin/agents', [
        'company_name' => 'Test Company',
        'contact_person' => 'John Doe',
        'email' => 'new@example.com',
        'status' => 'invalid_status',
    ]);

    $response->assertSessionHasErrors(['status']);
});

test('agent balance must be numeric', function () {
    $response = $this->loggedRequest->post('/admin/agents', [
        'company_name' => 'Test Company',
        'contact_person' => 'John Doe',
        'email' => 'new@example.com',
        'balance' => 'not_a_number',
    ]);

    $response->assertSessionHasErrors(['balance']);
});

test('agent credit limit must be non-negative', function () {
    $response = $this->loggedRequest->post('/admin/agents', [
        'company_name' => 'Test Company',
        'contact_person' => 'John Doe',
        'email' => 'new@example.com',
        'credit_limit' => -1000,
    ]);

    $response->assertSessionHasErrors(['credit_limit']);
});

test('agent user can be created', function () {
    $response = $this->loggedRequest->post('/admin/agents/'.$this->agent->uuid.'/create-user', [
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'email' => $this->agent->email,
        'profile_type' => 'agent',
        'profile_id' => $this->agent->id,
    ]);
});

test('agent user creation requires password', function () {
    $response = $this->loggedRequest->post('/admin/agents/'.$this->agent->uuid.'/create-user', [
        'password' => '',
        'password_confirmation' => '',
    ]);

    $response->assertSessionHasErrors(['password']);
});

test('agent user creation requires password confirmation', function () {
    $response = $this->loggedRequest->post('/admin/agents/'.$this->agent->uuid.'/create-user', [
        'password' => 'password123',
        'password_confirmation' => 'different',
    ]);

    $response->assertSessionHasErrors(['password']);
});

test('agent user cannot be created if user already exists', function () {
    \Modules\User\Models\User::create([
        'name' => $this->agent->contact_person,
        'email' => $this->agent->email,
        'password' => bcrypt('password'),
        'profile_type' => 'agent',
        'profile_id' => $this->agent->id,
    ]);

    $response = $this->loggedRequest->post('/admin/agents/'.$this->agent->uuid.'/create-user', [
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHas('error');
});
