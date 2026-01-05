<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Employee\Models\Designation;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Role::create(['name' => 'superadmin']);
    $this->user->assignRole('superadmin');

    $this->loggedRequest = $this->actingAs($this->user);

    $this->designation = Designation::factory()->create();
});

test('designation list can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/employee-designations');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Employee/DesignationIndex')
            ->has('designations.data', 1)
    );
});

test('designation create page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/employee-designations/create');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Employee/DesignationForm')
    );
});

test('designation can be created', function () {
    $response = $this->loggedRequest->post('/admin/employee-designations', [
        'name' => 'Test Designation',
        'code' => 'TEST01',
        'is_active' => true,
    ]);

    $response->assertRedirect('/admin/employee-designations');

    $this->assertDatabaseHas('employee_designations', [
        'name' => 'Test Designation',
        'code' => 'TEST01',
    ]);
});

test('designation edit page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/employee-designations/'.$this->designation->uuid.'/edit');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Employee/DesignationForm')
            ->has('designation')
    );
});

test('designation can be updated', function () {
    $response = $this->loggedRequest->put('/admin/employee-designations/'.$this->designation->uuid, [
        'name' => 'Updated Designation',
        'code' => $this->designation->code,
        'is_active' => true,
    ]);

    $response->assertRedirect('/admin/employee-designations');

    $this->assertDatabaseHas('employee_designations', [
        'id' => $this->designation->id,
        'name' => 'Updated Designation',
    ]);
});

test('designation can be deleted', function () {
    $response = $this->loggedRequest->delete('/admin/employee-designations/'.$this->designation->uuid);

    $response->assertRedirect('/admin/employee-designations');

    $this->assertSoftDeleted('employee_designations', [
        'id' => $this->designation->id,
    ]);
});

test('designation recycle bin can be rendered', function () {
    $this->designation->delete();

    $response = $this->loggedRequest->get('/admin/employee-designations/recycle-bin');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Employee/DesignationRecycleBin')
            ->has('designations.data', 1)
    );
});

test('designation can be restored from recycle bin', function () {
    $this->designation->delete();

    $response = $this->loggedRequest->post('/admin/employee-designations/recycle-bin/'.$this->designation->id.'/restore');

    $response->assertRedirect();

    $this->assertDatabaseHas('employee_designations', [
        'id' => $this->designation->id,
        'deleted_at' => null,
    ]);
});

test('designation validation requires name', function () {
    $response = $this->loggedRequest->post('/admin/employee-designations', [
        'name' => '',
        'code' => 'TEST01',
    ]);

    $response->assertSessionHasErrors(['name']);
});

test('designation code must be unique', function () {
    $response = $this->loggedRequest->post('/admin/employee-designations', [
        'name' => 'Another Designation',
        'code' => $this->designation->code,
    ]);

    $response->assertSessionHasErrors(['code']);
});
