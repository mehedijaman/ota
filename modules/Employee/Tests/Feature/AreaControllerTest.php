<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Employee\Models\Area;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Role::create(['name' => 'superadmin']);
    $this->user->assignRole('superadmin');

    $this->loggedRequest = $this->actingAs($this->user);

    $this->area = Area::factory()->create();
});

test('area list can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/employee-areas');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Employee/AreaIndex')
            ->has('areas.data', 1)
    );
});

test('area create page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/employee-areas/create');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Employee/AreaForm')
    );
});

test('area can be created', function () {
    $response = $this->loggedRequest->post('/admin/employee-areas', [
        'name' => 'Test Area',
        'code' => 'AREA01',
        'is_active' => true,
    ]);

    $response->assertRedirect('/admin/employee-areas');

    $this->assertDatabaseHas('employee_areas', [
        'name' => 'Test Area',
        'code' => 'AREA01',
    ]);
});

test('area edit page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/employee-areas/'.$this->area->uuid.'/edit');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Employee/AreaForm')
            ->has('area')
    );
});

test('area can be updated', function () {
    $response = $this->loggedRequest->put('/admin/employee-areas/'.$this->area->uuid, [
        'name' => 'Updated Area',
        'code' => $this->area->code,
        'is_active' => true,
    ]);

    $response->assertRedirect('/admin/employee-areas');

    $this->assertDatabaseHas('employee_areas', [
        'id' => $this->area->id,
        'name' => 'Updated Area',
    ]);
});

test('area can be deleted', function () {
    $response = $this->loggedRequest->delete('/admin/employee-areas/'.$this->area->uuid);

    $response->assertRedirect('/admin/employee-areas');

    $this->assertSoftDeleted('employee_areas', [
        'id' => $this->area->id,
    ]);
});

test('area recycle bin can be rendered', function () {
    $this->area->delete();

    $response = $this->loggedRequest->get('/admin/employee-areas/recycle-bin');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Employee/AreaRecycleBin')
            ->has('areas.data', 1)
    );
});

test('area can be restored from recycle bin', function () {
    $this->area->delete();

    $response = $this->loggedRequest->post('/admin/employee-areas/recycle-bin/'.$this->area->id.'/restore');

    $response->assertRedirect();

    $this->assertDatabaseHas('employee_areas', [
        'id' => $this->area->id,
        'deleted_at' => null,
    ]);
});

test('area validation requires name', function () {
    $response = $this->loggedRequest->post('/admin/employee-areas', [
        'name' => '',
        'code' => 'AREA01',
    ]);

    $response->assertSessionHasErrors(['name']);
});

test('area code must be unique', function () {
    $response = $this->loggedRequest->post('/admin/employee-areas', [
        'name' => 'Another Area',
        'code' => $this->area->code,
    ]);

    $response->assertSessionHasErrors(['code']);
});
