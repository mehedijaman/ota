<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Employee\Models\Area;
use Modules\Employee\Models\Department;
use Modules\Employee\Models\Designation;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\EmployeeBankAccount;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('employee has correct table name', function () {
    $employee = new Employee;
    expect($employee->getTable())->toBe('employees');
});

test('employee casts is_active to boolean', function () {
    $employee = Employee::factory()->create(['is_active' => 1]);
    expect($employee->is_active)->toBeBool();
});

test('employee generates code automatically', function () {
    $employee = Employee::factory()->create(['code' => null]);
    expect($employee->code)->not->toBeNull();
    expect($employee->code)->toStartWith('EMP-');
});

test('employee code is uppercased', function () {
    $employee = Employee::factory()->create(['code' => 'emp-test']);
    expect($employee->code)->toBe('EMP-TEST');
});

test('employee belongs to designation', function () {
    $designation = Designation::factory()->create();
    $employee = Employee::factory()->create(['designation_id' => $designation->id]);

    expect($employee->designation)->toBeInstanceOf(Designation::class);
    expect($employee->designation->id)->toBe($designation->id);
});

test('employee belongs to department', function () {
    $department = Department::factory()->create();
    $employee = Employee::factory()->create(['department_id' => $department->id]);

    expect($employee->department)->toBeInstanceOf(Department::class);
    expect($employee->department->id)->toBe($department->id);
});

test('employee belongs to area', function () {
    $area = Area::factory()->create();
    $employee = Employee::factory()->create(['area_id' => $area->id]);

    expect($employee->area)->toBeInstanceOf(Area::class);
    expect($employee->area->id)->toBe($area->id);
});

test('employee has bank account relationship', function () {
    $employee = Employee::factory()->create();
    expect($employee->bankAccount())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasOne::class);
});

test('employee bank account can be created', function () {
    $employee = Employee::factory()->create();
    $employee->bankAccount()->create([
        'bank_name' => 'Test Bank',
        'account_no' => '1234567890',
        'account_name' => 'Test Account',
    ]);

    expect($employee->bankAccount)->toBeInstanceOf(EmployeeBankAccount::class);
    expect($employee->bankAccount->bank_name)->toBe('Test Bank');
});

test('employee uses uuid as route key', function () {
    $employee = new Employee;
    expect($employee->getRouteKeyName())->toBe('uuid');
});

test('employee service length is calculated', function () {
    $employee = Employee::factory()->create([
        'joining_date' => now()->subYears(2)->subMonths(3),
    ]);

    expect($employee->service_length)->toContain('2 years');
});

test('employee photo url returns null when no photo', function () {
    $employee = Employee::factory()->create();
    expect($employee->photo_url)->toBeNull();
});

test('employee documents returns empty array when no documents', function () {
    $employee = Employee::factory()->create();
    expect($employee->documents)->toBeArray();
    expect($employee->documents)->toBeEmpty();
});
