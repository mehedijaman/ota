<?php

namespace Modules\Visa\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Modules\Visa\Models\VisaCategory;

class VisaCategoryService
{
    public function getAll(): LengthAwarePaginator
    {
        return VisaCategory::query()
            ->withCount('visas')
            ->when(request('status') !== null && request('status') !== '', function ($q) {
                $q->where('is_active', request('status') === 'active');
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 15))
            ->withQueryString()
            ->through(fn (VisaCategory $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'code' => $category->code,
                'slug' => $category->slug,
                'icon' => $category->icon,
                'color' => $category->color,
                'thumbnail_url' => $category->getFirstMediaUrl('image'),
                'visas_count' => $category->visas_count,
                'is_active' => $category->is_active,
                'sort_order' => $category->sort_order,
            ]);
    }

    /**
     * @return array<string, int>
     */
    public function getStats(): array
    {
        return [
            'total' => VisaCategory::count(),
            'active' => VisaCategory::where('is_active', true)->count(),
            'inactive' => VisaCategory::where('is_active', false)->count(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getForEdit(int $id): array
    {
        $category = VisaCategory::findOrFail($id);

        return [
            'id' => $category->id,
            'name' => $category->name,
            'code' => $category->code,
            'slug' => $category->slug,
            'description' => $category->description,
            'icon' => $category->icon,
            'color' => $category->color,
            'is_active' => $category->is_active,
            'sort_order' => $category->sort_order,
            'meta_title' => $category->meta_title,
            'meta_description' => $category->meta_description,
            'meta_keywords' => $category->meta_keywords,
            'image_url' => $category->getFirstMediaUrl('image'),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): VisaCategory
    {
        $category = VisaCategory::create($data);

        $this->handleMediaUploads($category, $data);

        return $category;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(VisaCategory $category, array $data): VisaCategory
    {
        $category->update($data);

        $this->handleMediaUploads($category, $data);

        return $category;
    }

    public function delete(VisaCategory $category): bool
    {
        if ($category->visas()->exists()) {
            return false;
        }

        $category->delete();

        return true;
    }

    public function restore(int $id): void
    {
        VisaCategory::withTrashed()->findOrFail($id)->restore();
    }

    public function forceDelete(int $id): void
    {
        $category = VisaCategory::withTrashed()->findOrFail($id);
        $category->clearMediaCollection('image');
        $category->forceDelete();
    }

    public function getRecycleBin(): LengthAwarePaginator
    {
        return VisaCategory::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(request('rowsPerPage', 15))
            ->withQueryString()
            ->through(fn (VisaCategory $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'code' => $category->code,
                'deleted_at' => $category->deleted_at->format('M d, Y'),
            ]);
    }

    public function emptyRecycleBin(): void
    {
        VisaCategory::onlyTrashed()->each(function (VisaCategory $category) {
            $category->clearMediaCollection('image');
            $category->forceDelete();
        });
    }

    public function restoreAll(): void
    {
        VisaCategory::onlyTrashed()->restore();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleMediaUploads(VisaCategory $category, array $data): void
    {
        if (! empty($data['remove_image'])) {
            $category->clearMediaCollection('image');
        }

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $category->clearMediaCollection('image');
            $category->addMedia($data['image'])->toMediaCollection('image');
        }
    }
}
