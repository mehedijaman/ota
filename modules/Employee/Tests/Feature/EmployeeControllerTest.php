<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Employee\Models\Area;
use Modules\Employee\Models\Department;
use Modules\Employee\Models\Designation;
use Modules\Employee\Models\Employee;
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
    $this->department = Department::factory()->create();
    $this->area = Area::factory()->create();
    $this->employee = Employee::factory()->create([
        'designation_id' => $this->designation->id,
        'department_id' => $this->department->id,
        'area_id' => $this->area->id,
    ]);
});

test('employee list can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/employees');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Employee/EmployeeIndex')
            ->has('employees.data', 1)
    );
});

test('employee create page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/employees/create');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Employee/EmployeeForm')
            ->has('designations')
    );
});

test('employee can be created', function () {
    $response = $this->loggedRequest->post('/admin/employees', [
        'name' => 'Test Employee',
        'designation_id' => $this->designation->id,
        'department_id' => $this->department->id,
        'area_id' => $this->area->id,
        'email' => 'test.employee@example.com',
        'phone' => '1234567890',
        'joining_date' => '2024-01-01',
        'gender' => 'Male',
    ]);

    $response->assertRedirect('/admin/employees');

    $this->assertDatabaseHas('employees', [
        'name' => 'Test Employee',
        'email' => 'test.employee@example.com',
    ]);
});

test('employee show page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/employees/'.$this->employee->uuid);

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Employee/EmployeeShow')
            ->has('employee')
    );
});

test('employee edit page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/employees/'.$this->employee->uuid.'/edit');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Employee/EmployeeForm')
            ->has('employee')
    );
});

test('employee can be updated', function () {
    $response = $this->loggedRequest->put('/admin/employees/'.$this->employee->uuid, [
        'name' => 'Updated Employee',
        'designation_id' => $this->designation->id,
        'email' => $this->employee->email,
    ]);

    $response->assertRedirect('/admin/employees');

    $this->assertDatabaseHas('employees', [
        'id' => $this->employee->id,
        'name' => 'Updated Employee',
    ]);
});

test('employee can be deleted', function () {
    $response = $this->loggedRequest->delete('/admin/employees/'.$this->employee->uuid);

    $response->assertRedirect('/admin/employees');

    $this->assertSoftDeleted('employees', [
        'id' => $this->employee->id,
    ]);
});

test('employee recycle bin can be rendered', function () {
    $this->employee->delete();

    $response = $this->loggedRequest->get('/admin/employees/recycle-bin');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Employee/EmployeeRecycleBin')
            ->has('employees.data', 1)
    );
});

test('employee validation requires name', function () {
    $response = $this->loggedRequest->post('/admin/employees', [
        'name' => '',
        'email' => 'test@example.com',
    ]);

    $response->assertSessionHasErrors(['name']);
});

test('employee email must be unique', function () {
    $response = $this->loggedRequest->post('/admin/employees', [
        'name' => 'Another Employee',
        'email' => $this->employee->email,
    ]);

    $response->assertSessionHasErrors(['email']);
});

test('employee bank account is created with employee', function () {
    $response = $this->loggedRequest->post('/admin/employees', [
        'name' => 'Employee With Bank',
        'email' => 'bankemployee@example.com',
        'bank_name' => 'Test Bank',
        'account_no' => '1234567890',
        'account_name' => 'Test Account',
    ]);

    $response->assertRedirect('/admin/employees');

    $employee = Employee::where('email', 'bankemployee@example.com')->first();

    $this->assertDatabaseHas('employee_bank_accounts', [
        'employee_id' => $employee->id,
        'bank_name' => 'Test Bank',
        'account_no' => '1234567890',
    ]);
});
