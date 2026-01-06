<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Customer\Models\Customer;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Role::create(['name' => 'superadmin']);
    $this->user->assignRole('superadmin');

    $this->loggedRequest = $this->actingAs($this->user);

    $this->customer = Customer::factory()->active()->create();
});

test('customer list can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/customers');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Customer/CustomerIndex')
            ->has('customers.data', 1)
    );
});

test('customer create page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/customers/create');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Customer/CustomerForm')
    );
});

test('customer can be created', function () {
    $response = $this->loggedRequest->post('/admin/customers', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
        'phone' => '1234567890',
        'gender' => 'male',
        'address' => '123 Test Street',
        'city' => 'Dhaka',
        'country' => 'Bangladesh',
        'status' => 'active',
    ]);

    $response->assertRedirect('/admin/customers');

    $this->assertDatabaseHas('customers', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
    ]);
});

test('customer show page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/customers/'.$this->customer->uuid);

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Customer/CustomerShow')
            ->has('customer')
    );
});

test('customer edit page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/customers/'.$this->customer->uuid.'/edit');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Customer/CustomerForm')
            ->has('customer')
    );
});

test('customer can be updated', function () {
    $response = $this->loggedRequest->put('/admin/customers/'.$this->customer->uuid, [
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'email' => $this->customer->email,
        'status' => 'suspended',
    ]);

    $response->assertRedirect('/admin/customers');

    $this->assertDatabaseHas('customers', [
        'id' => $this->customer->id,
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'status' => 'suspended',
    ]);
});

test('customer can be deleted', function () {
    $response = $this->loggedRequest->delete('/admin/customers/'.$this->customer->uuid);

    $response->assertRedirect('/admin/customers');

    $this->assertSoftDeleted('customers', [
        'id' => $this->customer->id,
    ]);
});

test('customer recycle bin can be rendered', function () {
    $this->customer->delete();

    $response = $this->loggedRequest->get('/admin/customers/recycle-bin');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Customer/CustomerRecycleBin')
            ->has('customers.data', 1)
    );
});

test('customer can be restored from recycle bin', function () {
    $this->customer->delete();

    $response = $this->loggedRequest->post('/admin/customers/recycle-bin/'.$this->customer->id.'/restore');

    $response->assertRedirect('/admin/customers/recycle-bin');

    $this->assertDatabaseHas('customers', [
        'id' => $this->customer->id,
        'deleted_at' => null,
    ]);
});

test('customer can be permanently deleted from recycle bin', function () {
    $this->customer->delete();

    $response = $this->loggedRequest->delete('/admin/customers/recycle-bin/'.$this->customer->id.'/force');

    $response->assertRedirect('/admin/customers/recycle-bin');

    $this->assertDatabaseMissing('customers', [
        'id' => $this->customer->id,
    ]);
});

test('customer recycle bin can be emptied', function () {
    $this->customer->delete();

    $response = $this->loggedRequest->delete('/admin/customers/recycle-bin/empty');

    $response->assertRedirect('/admin/customers/recycle-bin');

    $this->assertDatabaseMissing('customers', [
        'id' => $this->customer->id,
    ]);
});

test('all customers in recycle bin can be restored', function () {
    $customer2 = Customer::factory()->create();
    $this->customer->delete();
    $customer2->delete();

    $response = $this->loggedRequest->post('/admin/customers/recycle-bin/restore-all');

    $response->assertRedirect('/admin/customers/recycle-bin');

    $this->assertDatabaseHas('customers', [
        'id' => $this->customer->id,
        'deleted_at' => null,
    ]);

    $this->assertDatabaseHas('customers', [
        'id' => $customer2->id,
        'deleted_at' => null,
    ]);
});

test('customer validation requires first name', function () {
    $response = $this->loggedRequest->post('/admin/customers', [
        'first_name' => '',
        'last_name' => 'Doe',
        'email' => 'test@example.com',
    ]);

    $response->assertSessionHasErrors(['first_name']);
});

test('customer validation requires last name', function () {
    $response = $this->loggedRequest->post('/admin/customers', [
        'first_name' => 'John',
        'last_name' => '',
        'email' => 'test@example.com',
    ]);

    $response->assertSessionHasErrors(['last_name']);
});

test('customer validation requires email', function () {
    $response = $this->loggedRequest->post('/admin/customers', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => '',
    ]);

    $response->assertSessionHasErrors(['email']);
});

test('customer email must be unique', function () {
    $response = $this->loggedRequest->post('/admin/customers', [
        'first_name' => 'Another',
        'last_name' => 'Customer',
        'email' => $this->customer->email,
    ]);

    $response->assertSessionHasErrors(['email']);
});

test('customer status must be valid', function () {
    $response = $this->loggedRequest->post('/admin/customers', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'new@example.com',
        'status' => 'invalid_status',
    ]);

    $response->assertSessionHasErrors(['status']);
});

test('customer gender must be valid', function () {
    $response = $this->loggedRequest->post('/admin/customers', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'new@example.com',
        'gender' => 'invalid_gender',
    ]);

    $response->assertSessionHasErrors(['gender']);
});

test('customer user can be created', function () {
    $response = $this->loggedRequest->post('/admin/customers/'.$this->customer->uuid.'/create-user', [
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'email' => $this->customer->email,
        'profile_type' => 'customer',
        'profile_id' => $this->customer->id,
    ]);
});

test('customer user creation requires password', function () {
    $response = $this->loggedRequest->post('/admin/customers/'.$this->customer->uuid.'/create-user', [
        'password' => '',
        'password_confirmation' => '',
    ]);

    $response->assertSessionHasErrors(['password']);
});

test('customer user creation requires password confirmation', function () {
    $response = $this->loggedRequest->post('/admin/customers/'.$this->customer->uuid.'/create-user', [
        'password' => 'password123',
        'password_confirmation' => 'different',
    ]);

    $response->assertSessionHasErrors(['password']);
});

test('customer user cannot be created if user already exists', function () {
    \Modules\User\Models\User::create([
        'name' => $this->customer->full_name,
        'email' => $this->customer->email,
        'password' => bcrypt('password'),
        'profile_type' => 'customer',
        'profile_id' => $this->customer->id,
    ]);

    $response = $this->loggedRequest->post('/admin/customers/'.$this->customer->uuid.'/create-user', [
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHas('error');
});
