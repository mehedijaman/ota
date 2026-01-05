<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Employee\Models\Department;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Role::create(['name' => 'superadmin']);
    $this->user->assignRole('superadmin');

    $this->loggedRequest = $this->actingAs($this->user);

    $this->department = Department::factory()->create();
});

test('department list can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/employee-departments');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Employee/DepartmentIndex')
            ->has('departments.data', 1)
    );
});

test('department create page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/employee-departments/create');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Employee/DepartmentForm')
    );
});

test('department can be created', function () {
    $response = $this->loggedRequest->post('/admin/employee-departments', [
        'name' => 'Test Department',
        'code' => 'DEPT01',
        'is_active' => true,
    ]);

    $response->assertRedirect('/admin/employee-departments');

    $this->assertDatabaseHas('employee_departments', [
        'name' => 'Test Department',
        'code' => 'DEPT01',
    ]);
});

test('department edit page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/employee-departments/'.$this->department->uuid.'/edit');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Employee/DepartmentForm')
            ->has('department')
    );
});

test('department can be updated', function () {
    $response = $this->loggedRequest->put('/admin/employee-departments/'.$this->department->uuid, [
        'name' => 'Updated Department',
        'code' => $this->department->code,
        'is_active' => true,
    ]);

    $response->assertRedirect('/admin/employee-departments');

    $this->assertDatabaseHas('employee_departments', [
        'id' => $this->department->id,
        'name' => 'Updated Department',
    ]);
});

test('department can be deleted', function () {
    $response = $this->loggedRequest->delete('/admin/employee-departments/'.$this->department->uuid);

    $response->assertRedirect('/admin/employee-departments');

    $this->assertSoftDeleted('employee_departments', [
        'id' => $this->department->id,
    ]);
});

test('department recycle bin can be rendered', function () {
    $this->department->delete();

    $response = $this->loggedRequest->get('/admin/employee-departments/recycle-bin');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Employee/DepartmentRecycleBin')
            ->has('departments.data', 1)
    );
});

test('department can be restored from recycle bin', function () {
    $this->department->delete();

    $response = $this->loggedRequest->post('/admin/employee-departments/recycle-bin/'.$this->department->id.'/restore');

    $response->assertRedirect();

    $this->assertDatabaseHas('employee_departments', [
        'id' => $this->department->id,
        'deleted_at' => null,
    ]);
});

test('department validation requires name', function () {
    $response = $this->loggedRequest->post('/admin/employee-departments', [
        'name' => '',
        'code' => 'DEPT01',
    ]);

    $response->assertSessionHasErrors(['name']);
});

test('department code must be unique', function () {
    $response = $this->loggedRequest->post('/admin/employee-departments', [
        'name' => 'Another Department',
        'code' => $this->department->code,
    ]);

    $response->assertSessionHasErrors(['code']);
});
