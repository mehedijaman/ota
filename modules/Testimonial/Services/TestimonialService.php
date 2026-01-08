<?php

namespace Modules\Testimonial\Services;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Modules\Testimonial\Models\Testimonial;

class TestimonialService
{
    /**
     * Get paginated list of all testimonials.
     */
    public function getAll()
    {
        return Testimonial::query()
            ->latest()
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($testimonial) => [
                'id' => $testimonial->id,
                'name' => $testimonial->name,
                'designation' => $testimonial->designation,
                'company' => $testimonial->company,
                'title' => $testimonial->title,
                'description' => $testimonial->description,
                'image_url' => $testimonial->image_url,
                'is_active' => $testimonial->is_active,
                'featured' => $testimonial->featured,
            ]);
    }

    /**
     * Get paginated list of trashed testimonials.
     */
    public function getRecycleBin()
    {
        return Testimonial::onlyTrashed()
            ->orderBy('id')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($testimonial) => [
                'id' => $testimonial->id,
                'name' => $testimonial->name,
                'designation' => $testimonial->designation,
                'company' => $testimonial->company,
                'image_url' => $testimonial->image_url,
                'is_active' => $testimonial->is_active,
                'deleted_at' => $testimonial->deleted_at ? Carbon::parse($testimonial->deleted_at)->format('d/m/Y') : null,
                'deletedBy' => $testimonial->deletedBy,
            ]);
    }

    /**
     * Create a new testimonial with media handling.
     */
    public function create(array $data): Testimonial
    {
        return DB::transaction(function () use ($data) {
            $testimonial = Testimonial::create($this->prepareData($data));

            $this->handleMediaUpload($testimonial, $data);

            return $testimonial;
        });
    }

    /**
     * Update an existing testimonial with media handling.
     */
    public function update(Testimonial $testimonial, array $data): Testimonial
    {
        return DB::transaction(function () use ($testimonial, $data) {
            $testimonial->update($this->prepareData($data));

            $this->handleMediaUpdate($testimonial, $data);

            return $testimonial;
        });
    }

    /**
     * Soft delete a testimonial.
     */
    public function delete(Testimonial $testimonial): void
    {
        $testimonial->delete();
    }

    /**
     * Restore a soft-deleted testimonial.
     */
    public function restore(int $id): void
    {
        Testimonial::onlyTrashed()->findOrFail($id)->restore();
    }

    /**
     * Permanently delete a testimonial and its media.
     */
    public function forceDelete(int $id): void
    {
        $testimonial = Testimonial::onlyTrashed()->findOrFail($id);
        $testimonial->clearMediaCollection('image');
        $testimonial->forceDelete();
    }

    /**
     * Empty the recycle bin.
     */
    public function emptyRecycleBin(): void
    {
        $testimonials = Testimonial::onlyTrashed()->get();
        foreach ($testimonials as $testimonial) {
            $testimonial->clearMediaCollection('image');
            $testimonial->forceDelete();
        }
    }

    /**
     * Restore all trashed testimonials.
     */
    public function restoreAll(): void
    {
        Testimonial::onlyTrashed()->restore();
    }

    /**
     * Prepare data for creating or updating a testimonial.
     */
    protected function prepareData(array $data): array
    {
        return [
            'name' => $data['name'] ?? null,
            'designation' => $data['designation'] ?? null,
            'company' => $data['company'] ?? null,
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'featured' => $data['featured'] ?? false,
            'is_active' => $data['is_active'] ?? true,
        ];
    }

    /**
     * Handle media upload for a new testimonial.
     */
    protected function handleMediaUpload(Testimonial $testimonial, array $data): void
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $testimonial->addMedia($data['image'])->toMediaCollection('image');
        }
    }

    /**
     * Handle media update for an existing testimonial.
     */
    protected function handleMediaUpdate(Testimonial $testimonial, array $data): void
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $testimonial->clearMediaCollection('image');
            $testimonial->addMedia($data['image'])->toMediaCollection('image');
        } elseif (! empty($data['remove_previous_image'])) {
            $testimonial->clearMediaCollection('image');
        }
    }
}
