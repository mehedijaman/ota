<?php

namespace Modules\Page\Services;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Modules\Page\Models\Page;

class PageService
{
    /**
     * Get paginated list of all pages.
     */
    public function getAll()
    {
        return Page::query()
            ->orderByDesc('id')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($page) => [
                'id' => $page->id,
                'slug' => $page->slug,
                'title' => $page->title,
                'content' => $page->content,
                'image_url' => $page->image_url,
                'status' => $page->status,
                'published_at' => $page->published_at_formatted,
                'active' => $page->active,
            ]);
    }

    /**
     * Get paginated list of trashed pages.
     */
    public function getRecycleBin()
    {
        return Page::onlyTrashed()
            ->orderBy('id')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($page) => [
                'id' => $page->id,
                'slug' => $page->slug,
                'title' => $page->title,
                'image_url' => $page->image_url,
                'status' => $page->status,
                'deleted_at' => $page->deleted_at ? Carbon::parse($page->deleted_at)->format('d/m/Y') : null,
                'deletedBy' => $page->deletedBy,
            ]);
    }

    /**
     * Create a new page with media handling.
     */
    public function create(array $data): Page
    {
        return DB::transaction(function () use ($data) {
            $page = Page::create($this->prepareData($data));

            $this->handleMediaUpload($page, $data);

            return $page;
        });
    }

    /**
     * Update an existing page with media handling.
     */
    public function update(Page $page, array $data): Page
    {
        return DB::transaction(function () use ($page, $data) {
            $page->update($this->prepareData($data));

            $this->handleMediaUpdate($page, $data);

            return $page;
        });
    }

    /**
     * Soft delete a page.
     */
    public function delete(Page $page): void
    {
        $page->delete();
    }

    /**
     * Restore a soft-deleted page.
     */
    public function restore(int $id): void
    {
        Page::onlyTrashed()->findOrFail($id)->restore();
    }

    /**
     * Permanently delete a page and its media.
     */
    public function forceDelete(int $id): void
    {
        $page = Page::onlyTrashed()->findOrFail($id);
        $page->clearMediaCollection('featured_image');
        $page->forceDelete();
    }

    /**
     * Empty the recycle bin.
     */
    public function emptyRecycleBin(): void
    {
        $pages = Page::onlyTrashed()->get();
        foreach ($pages as $page) {
            $page->clearMediaCollection('featured_image');
            $page->forceDelete();
        }
    }

    /**
     * Restore all trashed pages.
     */
    public function restoreAll(): void
    {
        Page::onlyTrashed()->restore();
    }

    /**
     * Prepare data for creating or updating a page.
     */
    protected function prepareData(array $data): array
    {
        return [
            'title' => $data['title'] ?? null,
            'slug' => $data['slug'] ?? null,
            'content' => $data['content'] ?? null,
            'meta_tag_title' => $data['meta_tag_title'] ?? null,
            'meta_tag_description' => $data['meta_tag_description'] ?? null,
            'published_at' => $data['published_at'] ?? null,
            'active' => $data['active'] ?? true,
        ];
    }

    /**
     * Handle media upload for a new page.
     */
    protected function handleMediaUpload(Page $page, array $data): void
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $page->addMedia($data['image'])->toMediaCollection('featured_image');
        }
    }

    /**
     * Handle media update for an existing page.
     */
    protected function handleMediaUpdate(Page $page, array $data): void
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $page->clearMediaCollection('featured_image');
            $page->addMedia($data['image'])->toMediaCollection('featured_image');
        } elseif (! empty($data['remove_previous_image'])) {
            $page->clearMediaCollection('featured_image');
        }
    }
}
