<?php

namespace Modules\Faq\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Faq\Models\Faq;

class FaqService
{
    public function getAll(): LengthAwarePaginator
    {
        return Faq::query()
            ->with('category:id,name')
            ->search(request('searchContext'), request('searchTerm'))
            ->orderBy('sort_order')
            ->orderBy('id', 'desc')
            ->paginate(request('rowsPerPage', 20))
            ->withQueryString()
            ->through(fn ($faq) => [
                'id' => $faq->id,
                'uuid' => $faq->uuid,
                'question' => $faq->question,
                'category' => $faq->category?->name,
                'status' => $faq->status,
                'is_featured' => $faq->is_featured,
                'target_audience' => $faq->target_audience,
                'sort_order' => $faq->sort_order,
                'updated_at' => $faq->updated_at->format('d/m/Y H:i').'h',
            ]);
    }

    public function getRecycleBin(): LengthAwarePaginator
    {
        return Faq::onlyTrashed()
            ->with('category:id,name')
            ->orderBy('deleted_at', 'desc')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 20))
            ->withQueryString()
            ->through(fn ($faq) => [
                'id' => $faq->id,
                'uuid' => $faq->uuid,
                'question' => $faq->question,
                'category' => $faq->category?->name,
                'status' => $faq->status,
                'deleted_at' => $faq->deleted_at->format('d/m/Y H:i').'h',
            ]);
    }

    public function create(array $data): Faq
    {
        return Faq::create($data);
    }

    public function update(Faq $faq, array $data): Faq
    {
        $faq->update($data);

        return $faq->fresh();
    }

    public function delete(Faq $faq): bool
    {
        return $faq->delete();
    }

    public function restore(string $uuid): Faq
    {
        $faq = Faq::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $faq->restore();

        return $faq;
    }

    public function forceDelete(string $uuid): bool
    {
        $faq = Faq::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

        return $faq->forceDelete();
    }

    public function emptyRecycleBin(): int
    {
        $count = Faq::onlyTrashed()->count();
        Faq::onlyTrashed()->forceDelete();

        return $count;
    }

    public function restoreAll(): int
    {
        $count = Faq::onlyTrashed()->count();
        Faq::onlyTrashed()->restore();

        return $count;
    }
}
