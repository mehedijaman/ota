<?php

namespace Modules\Tour\Services;

use Modules\Tour\Models\TourCategory;

class GetTourCategoryOptions
{
    public function get(): array
    {
        return TourCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($cat) => ['value' => $cat->id, 'label' => $cat->name])
            ->toArray();
    }
}
