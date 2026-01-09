<?php

namespace Modules\Tour\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Modules\Tour\Models\TourCategory;

class TourCategoryService
{
    public function getAll()
    {
        return TourCategory::query()
            ->withCount('tours')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 15))
            ->withQueryString()
            ->through(fn ($category) => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'icon' => $category->icon,
                'image_url' => $category->getFirstMediaUrl('image'),
                'tours_count' => $category->tours_count,
                'is_active' => $category->is_active,
                'sort_order' => $category->sort_order,
            ]);
    }

    public function create(array $data): TourCategory
    {
        return DB::transaction(function () use ($data) {
            $category = TourCategory::create($data);

            $this->handleMediaUploads($category, $data);

            return $category;
        });
    }

    public function update(TourCategory $category, array $data): TourCategory
    {
        return DB::transaction(function () use ($category, $data) {
            $category->update($data);

            $this->handleMediaUpdates($category, $data);

            return $category;
        });
    }

    public function delete(TourCategory $category): bool
    {
        if ($category->tours()->exists()) {
            return false;
        }

        return $category->delete();
    }

    protected function handleMediaUploads(TourCategory $category, array $data): void
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $category->addMedia($data['image'])->toMediaCollection('image');
        }
    }

    protected function handleMediaUpdates(TourCategory $category, array $data): void
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $category->clearMediaCollection('image');
            $category->addMedia($data['image'])->toMediaCollection('image');
        } elseif (! empty($data['remove_image'])) {
            $category->clearMediaCollection('image');
        }
    }
}
