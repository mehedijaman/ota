<?php

namespace Modules\Tour\Services;

use Modules\Tour\Models\Tour;

class GetTourOptions
{
    public function get(): array
    {
        return Tour::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code'])
            ->map(fn ($tour) => [
                'value' => $tour->id,
                'label' => $tour->code ? "[{$tour->code}] {$tour->name}" : $tour->name,
            ])
            ->toArray();
    }

    public function getWithPricing(): array
    {
        return Tour::query()
            ->where('is_active', true)
            ->with([
                'pricings' => fn ($q) => $q->where('is_active', true)->orderBy('name'),
                'extras' => fn ($q) => $q->where('is_active', true)->orderBy('name'),
            ])
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'base_price', 'discount_price', 'child_price', 'infant_price'])
            ->map(fn ($tour) => [
                'id' => $tour->id,
                'name' => $tour->code ? "[{$tour->code}] {$tour->name}" : $tour->name,
                'base_price' => $tour->discount_price ?? $tour->base_price,
                'child_price' => $tour->child_price,
                'infant_price' => $tour->infant_price,
                'pricings' => $tour->pricings->map(fn ($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'adult_price' => $p->adult_price,
                    'child_price' => $p->child_price,
                    'infant_price' => $p->infant_price,
                ])->toArray(),
                'extras' => $tour->extras->map(fn ($e) => [
                    'id' => $e->id,
                    'name' => $e->name,
                    'description' => $e->description,
                    'price' => $e->price,
                    'price_type' => $e->price_type,
                    'max_quantity' => $e->max_quantity,
                    'is_required' => $e->is_required,
                ])->toArray(),
            ])
            ->toArray();
    }
}
