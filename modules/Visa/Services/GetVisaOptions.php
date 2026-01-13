<?php

namespace Modules\Visa\Services;

use Illuminate\Support\Collection;
use Modules\Visa\Models\Visa;

class GetVisaOptions
{
    /**
     * @return Collection<int, array{id: int, name: string, code: string, country: string|null, category: string|null}>
     */
    public function get(): Collection
    {
        return Visa::query()
            ->active()
            ->with(['country', 'category'])
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'country_id', 'category_id'])
            ->map(fn (Visa $visa) => [
                'id' => $visa->id,
                'name' => $visa->name,
                'code' => $visa->code,
                'country' => $visa->country?->name,
                'category' => $visa->category?->name,
            ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function getWithPricing(): Collection
    {
        return Visa::query()
            ->active()
            ->with(['country', 'category'])
            ->orderBy('name')
            ->get()
            ->map(fn (Visa $visa) => [
                'id' => $visa->id,
                'name' => $visa->name,
                'code' => $visa->code,
                'country' => $visa->country?->name,
                'country_id' => $visa->country_id,
                'category' => $visa->category?->name,
                'category_id' => $visa->category_id,
                'base_price' => $visa->base_price,
                'service_fee' => $visa->service_fee,
                'embassy_fee' => $visa->embassy_fee,
                'vfs_fee' => $visa->vfs_fee,
                'total_price' => $visa->total_price,
                'processing_days' => $visa->processing_days_min.'-'.$visa->processing_days_max,
            ]);
    }
}
