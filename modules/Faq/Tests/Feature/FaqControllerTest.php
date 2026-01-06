<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Faq\Models\Faq;
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

it('faq list can be rendered', function () {
    $this->loggedRequest
        ->get(route('faq.index'))
        ->assertSuccessful();
});

it('faq create page can be rendered', function () {
    $this->loggedRequest
        ->get(route('faq.create'))
        ->assertSuccessful();
});

it('faq can be created', function () {
    $category = FaqCategory::factory()->create();

    $this->loggedRequest
        ->post(route('faq.store'), [
            'faq_category_id' => $category->id,
            'question' => 'What is this FAQ about?',
            'answer' => 'This is a test answer.',
            'status' => 'published',
            'sort_order' => 1,
            'is_featured' => true,
            'target_audience' => 'all',
        ])
        ->assertRedirect(route('faq.index'));

    $this->assertDatabaseHas('faqs', [
        'question' => 'What is this FAQ about?',
    ]);
});

it('faq show page can be rendered', function () {
    $faq = Faq::factory()->create();

    $this->loggedRequest
        ->get(route('faq.show', $faq->uuid))
        ->assertSuccessful();
});

it('faq edit page can be rendered', function () {
    $faq = Faq::factory()->create();

    $this->loggedRequest
        ->get(route('faq.edit', $faq->uuid))
        ->assertSuccessful();
});

it('faq can be updated', function () {
    $faq = Faq::factory()->create();

    $this->loggedRequest
        ->put(route('faq.update', $faq->uuid), [
            'question' => 'Updated question?',
            'answer' => 'Updated answer.',
            'status' => 'archived',
            'sort_order' => 5,
            'is_featured' => false,
            'target_audience' => 'b2b',
        ])
        ->assertRedirect(route('faq.index'));

    $this->assertDatabaseHas('faqs', [
        'id' => $faq->id,
        'question' => 'Updated question?',
    ]);
});

it('faq can be deleted', function () {
    $faq = Faq::factory()->create();

    $this->loggedRequest
        ->delete(route('faq.destroy', $faq->uuid))
        ->assertRedirect(route('faq.index'));

    $this->assertSoftDeleted('faqs', [
        'id' => $faq->id,
    ]);
});

it('faq recycle bin can be rendered', function () {
    $this->loggedRequest
        ->get(route('faq.recycleBin'))
        ->assertSuccessful();
});

it('faq can be restored from recycle bin', function () {
    $faq = Faq::factory()->create();
    $faq->delete();

    $this->loggedRequest
        ->put(route('faq.restore', $faq->uuid))
        ->assertRedirect(route('faq.recycleBin'));

    $this->assertDatabaseHas('faqs', [
        'id' => $faq->id,
        'deleted_at' => null,
    ]);
});

it('faq can be permanently deleted from recycle bin', function () {
    $faq = Faq::factory()->create();
    $faq->delete();

    $this->loggedRequest
        ->delete(route('faq.destroyForce', $faq->uuid))
        ->assertRedirect(route('faq.recycleBin'));

    $this->assertDatabaseMissing('faqs', [
        'id' => $faq->id,
    ]);
});

it('faq recycle bin can be emptied', function () {
    $faqs = Faq::factory()->count(3)->create();
    foreach ($faqs as $faq) {
        $faq->delete();
    }

    $this->loggedRequest
        ->delete(route('faq.emptyRecycleBin'))
        ->assertRedirect(route('faq.recycleBin'));

    expect(Faq::withTrashed()->count())->toBe(0);
});

it('all faqs in recycle bin can be restored', function () {
    $faqs = Faq::factory()->count(3)->create();
    foreach ($faqs as $faq) {
        $faq->delete();
    }

    $this->loggedRequest
        ->put(route('faq.restoreRecycleBin'))
        ->assertRedirect(route('faq.recycleBin'));

    expect(Faq::onlyTrashed()->count())->toBe(0);
    expect(Faq::count())->toBe(3);
});

it('faq validation requires question', function () {
    $this->loggedRequest
        ->post(route('faq.store'), [
            'answer' => 'Test answer',
            'status' => 'draft',
        ])
        ->assertSessionHasErrors('question');
});

it('faq validation requires answer', function () {
    $this->loggedRequest
        ->post(route('faq.store'), [
            'question' => 'Test question?',
            'status' => 'draft',
        ])
        ->assertSessionHasErrors('answer');
});

it('faq validation requires status', function () {
    $this->loggedRequest
        ->post(route('faq.store'), [
            'question' => 'Test question?',
            'answer' => 'Test answer',
        ])
        ->assertSessionHasErrors('status');
});

it('faq status must be valid', function () {
    $this->loggedRequest
        ->post(route('faq.store'), [
            'question' => 'Test question?',
            'answer' => 'Test answer',
            'status' => 'invalid_status',
        ])
        ->assertSessionHasErrors('status');
});

it('faq target audience must be valid', function () {
    $this->loggedRequest
        ->post(route('faq.store'), [
            'question' => 'Test question?',
            'answer' => 'Test answer',
            'status' => 'draft',
            'target_audience' => 'invalid_audience',
        ])
        ->assertSessionHasErrors('target_audience');
});
