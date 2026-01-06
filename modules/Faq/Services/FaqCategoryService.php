<?php

namespace Modules\Faq\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Faq\Models\FaqCategory;

class FaqCategoryService
{
    public function getAll(): LengthAwarePaginator
    {
        return FaqCategory::query()
            ->withCount('faqs')
            ->search(request('searchContext'), request('searchTerm'))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(request('rowsPerPage', 20))
            ->withQueryString()
            ->through(fn ($category) => [
                'id' => $category->id,
                'uuid' => $category->uuid,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'sort_order' => $category->sort_order,
                'is_active' => $category->is_active,
                'faqs_count' => $category->faqs_count,
                'updated_at' => $category->updated_at->format('d/m/Y H:i').'h',
            ]);
    }

    public function getRecycleBin(): LengthAwarePaginator
    {
        return FaqCategory::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 20))
            ->withQueryString()
            ->through(fn ($category) => [
                'id' => $category->id,
                'uuid' => $category->uuid,
                'name' => $category->name,
                'slug' => $category->slug,
                'is_active' => $category->is_active,
                'deleted_at' => $category->deleted_at->format('d/m/Y H:i').'h',
            ]);
    }

    public function create(array $data): FaqCategory
    {
        return FaqCategory::create($data);
    }

    public function update(FaqCategory $category, array $data): FaqCategory
    {
        $category->update($data);

        return $category->fresh();
    }

    public function delete(FaqCategory $category): bool
    {
        return $category->delete();
    }

    public function restore(string $uuid): FaqCategory
    {
        $category = FaqCategory::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $category->restore();

        return $category;
    }

    public function forceDelete(string $uuid): bool
    {
        $category = FaqCategory::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

        return $category->forceDelete();
    }

    public function emptyRecycleBin(): int
    {
        $count = FaqCategory::onlyTrashed()->count();
        FaqCategory::onlyTrashed()->forceDelete();

        return $count;
    }

    public function restoreAll(): int
    {
        $count = FaqCategory::onlyTrashed()->count();
        FaqCategory::onlyTrashed()->restore();

        return $count;
    }

    public function getOptions(): array
    {
        return FaqCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn ($category) => [
                'value' => $category->id,
                'label' => $category->name,
            ])
            ->toArray();
    }
}
