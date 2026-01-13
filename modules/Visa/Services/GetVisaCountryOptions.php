<?php

namespace Modules\Visa\Services;

use Illuminate\Support\Collection;
use Modules\Visa\Models\VisaCountry;

class GetVisaCountryOptions
{
    /**
     * @return Collection<int, array{id: int, name: string, code: string}>
     */
    public function get(): Collection
    {
        return VisaCountry::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'code'])
            ->map(fn (VisaCountry $country) => [
                'id' => $country->id,
                'name' => $country->name,
                'code' => $country->code,
            ]);
    }
}
