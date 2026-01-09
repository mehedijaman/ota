<?php

namespace Modules\Tour\Services;

use Modules\Tour\Models\TourDestination;

class GetTourDestinationOptions
{
    public function get(): array
    {
        return TourDestination::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'country'])
            ->map(fn ($dest) => ['value' => $dest->id, 'label' => $dest->name.($dest->country ? ', '.$dest->country : '')])
            ->toArray();
    }
}
