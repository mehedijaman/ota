<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Faq\Models\FaqCategory;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Role::create(['name' => 'superadmin']);
    $this->user->assignRole('superadmin');

    $this->loggedRequest = $this->actingAs($this->user);
});

it('faq category list can be rendered', function () {
    $this->loggedRequest
        ->get(route('faqCategory.index'))
        ->assertSuccessful();
});

it('faq category create page can be rendered', function () {
    $this->loggedRequest
        ->get(route('faqCategory.create'))
        ->assertSuccessful();
});

it('faq category can be created', function () {
    $this->loggedRequest
        ->post(route('faqCategory.store'), [
            'name' => 'Test Category',
            'description' => 'Test description',
            'sort_order' => 1,
            'is_active' => true,
        ])
        ->assertRedirect(route('faqCategory.index'));

    $this->assertDatabaseHas('faq_categories', [
        'name' => 'Test Category',
    ]);
});

it('faq category edit page can be rendered', function () {
    $category = FaqCategory::factory()->create();

    $this->loggedRequest
        ->get(route('faqCategory.edit', $category->uuid))
        ->assertSuccessful();
});

it('faq category can be updated', function () {
    $category = FaqCategory::factory()->create();

    $this->loggedRequest
        ->put(route('faqCategory.update', $category->uuid), [
            'name' => 'Updated Category',
            'description' => 'Updated description',
            'sort_order' => 5,
            'is_active' => false,
        ])
        ->assertRedirect(route('faqCategory.index'));

    $this->assertDatabaseHas('faq_categories', [
        'id' => $category->id,
        'name' => 'Updated Category',
    ]);
});

it('faq category can be deleted', function () {
    $category = FaqCategory::factory()->create();

    $this->loggedRequest
        ->delete(route('faqCategory.destroy', $category->uuid))
        ->assertRedirect(route('faqCategory.index'));

    $this->assertSoftDeleted('faq_categories', [
        'id' => $category->id,
    ]);
});

it('faq category recycle bin can be rendered', function () {
    $this->loggedRequest
        ->get(route('faqCategory.recycleBin'))
        ->assertSuccessful();
});

it('faq category can be restored from recycle bin', function () {
    $category = FaqCategory::factory()->create();
    $category->delete();

    $this->loggedRequest
        ->put(route('faqCategory.restore', $category->uuid))
        ->assertRedirect(route('faqCategory.recycleBin'));

    $this->assertDatabaseHas('faq_categories', [
        'id' => $category->id,
        'deleted_at' => null,
    ]);
});

it('faq category can be permanently deleted from recycle bin', function () {
    $category = FaqCategory::factory()->create();
    $category->delete();

    $this->loggedRequest
        ->delete(route('faqCategory.destroyForce', $category->uuid))
        ->assertRedirect(route('faqCategory.recycleBin'));

    $this->assertDatabaseMissing('faq_categories', [
        'id' => $category->id,
    ]);
});

it('faq category recycle bin can be emptied', function () {
    $categories = FaqCategory::factory()->count(3)->create();
    foreach ($categories as $category) {
        $category->delete();
    }

    $this->loggedRequest
        ->delete(route('faqCategory.emptyRecycleBin'))
        ->assertRedirect(route('faqCategory.recycleBin'));

    expect(FaqCategory::withTrashed()->count())->toBe(0);
});

it('all faq categories in recycle bin can be restored', function () {
    $categories = FaqCategory::factory()->count(3)->create();
    foreach ($categories as $category) {
        $category->delete();
    }

    $this->loggedRequest
        ->put(route('faqCategory.restoreRecycleBin'))
        ->assertRedirect(route('faqCategory.recycleBin'));

    expect(FaqCategory::onlyTrashed()->count())->toBe(0);
    expect(FaqCategory::count())->toBe(3);
});

it('faq category validation requires name', function () {
    $this->loggedRequest
        ->post(route('faqCategory.store'), [
            'description' => 'Test description',
        ])
        ->assertSessionHasErrors('name');
});
