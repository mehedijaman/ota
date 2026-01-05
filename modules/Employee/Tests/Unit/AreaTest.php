<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Employee\Models\Area;
use Modules\Employee\Models\Employee;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('area has correct table name', function () {
    $area = new Area;
    expect($area->getTable())->toBe('employee_areas');
});

test('area has fillable attributes', function () {
    $area = new Area;
    expect($area->getFillable())->toContain('name', 'code', 'is_active');
});

test('area casts is_active to boolean', function () {
    $area = Area::factory()->create(['is_active' => 1]);
    expect($area->is_active)->toBeBool();
});

test('area generates code automatically', function () {
    $area = Area::factory()->create(['code' => null]);
    expect($area->code)->not->toBeNull();
    expect($area->code)->toStartWith('AR');
});

test('area code is uppercased', function () {
    $area = Area::factory()->create(['code' => 'ho']);
    expect($area->code)->toBe('HO');
});

test('area has employees relationship', function () {
    $area = Area::factory()->create();
    expect($area->employees())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

test('area uses uuid as route key', function () {
    $area = new Area;
    expect($area->getRouteKeyName())->toBe('uuid');
});

test('area employee count method works', function () {
    $area = Area::factory()->create();
    Employee::factory()->count(4)->create(['area_id' => $area->id]);

    expect($area->employeeCount())->toBe(4);
});
