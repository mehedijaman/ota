<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Employee\Models\Department;
use Modules\Employee\Models\Employee;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('department has correct table name', function () {
    $department = new Department;
    expect($department->getTable())->toBe('employee_departments');
});

test('department has fillable attributes', function () {
    $department = new Department;
    expect($department->getFillable())->toContain('name', 'code', 'is_active');
});

test('department casts is_active to boolean', function () {
    $department = Department::factory()->create(['is_active' => 1]);
    expect($department->is_active)->toBeBool();
});

test('department generates code automatically', function () {
    $department = Department::factory()->create(['code' => null]);
    expect($department->code)->not->toBeNull();
    expect($department->code)->toStartWith('DEP');
});

test('department code is uppercased', function () {
    $department = Department::factory()->create(['code' => 'hr']);
    expect($department->code)->toBe('HR');
});

test('department has employees relationship', function () {
    $department = Department::factory()->create();
    expect($department->employees())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

test('department uses uuid as route key', function () {
    $department = new Department;
    expect($department->getRouteKeyName())->toBe('uuid');
});

test('department employee count method works', function () {
    $department = Department::factory()->create();
    Employee::factory()->count(2)->create(['department_id' => $department->id]);

    expect($department->employeeCount())->toBe(2);
});
