<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Employee\Models\Designation;
use Modules\Employee\Models\Employee;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('designation has correct table name', function () {
    $designation = new Designation;
    expect($designation->getTable())->toBe('employee_designations');
});

test('designation has fillable attributes', function () {
    $designation = new Designation;
    expect($designation->getFillable())->toContain('name', 'code', 'is_active');
});

test('designation casts is_active to boolean', function () {
    $designation = Designation::factory()->create(['is_active' => 1]);
    expect($designation->is_active)->toBeBool();
});

test('designation generates code automatically', function () {
    $designation = Designation::factory()->create(['code' => null]);
    expect($designation->code)->not->toBeNull();
    expect($designation->code)->toStartWith('ED');
});

test('designation code is uppercased', function () {
    $designation = Designation::factory()->create(['code' => 'test123']);
    expect($designation->code)->toBe('TEST123');
});

test('designation has employees relationship', function () {
    $designation = Designation::factory()->create();
    expect($designation->employees())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

test('designation uses uuid as route key', function () {
    $designation = new Designation;
    expect($designation->getRouteKeyName())->toBe('uuid');
});

test('designation employee count method works', function () {
    $designation = Designation::factory()->create();
    Employee::factory()->count(3)->create(['designation_id' => $designation->id]);

    expect($designation->employeeCount())->toBe(3);
});
