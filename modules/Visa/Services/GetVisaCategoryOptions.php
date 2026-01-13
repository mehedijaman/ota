<?php

namespace Modules\Visa\Services;

use Illuminate\Support\Collection;
use Modules\Visa\Models\VisaCategory;

class GetVisaCategoryOptions
{
    /**
     * @return Collection<int, array{id: int, name: string, code: string}>
     */
    public function get(): Collection
    {
        return VisaCategory::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'code'])
            ->map(fn (VisaCategory $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'code' => $category->code,
            ]);
    }
}
