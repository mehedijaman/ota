<?php

namespace Modules\Visa\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Visa\Models\Visa;
use Modules\Visa\Models\VisaCategory;
use Modules\Visa\Models\VisaCountry;

class SiteVisaService
{
    /**
     * Get featured visas for homepage.
     *
     * @return Collection<int, Visa>
     */
    public static function getFeaturedVisas(int $limit = 8): Collection
    {
        return Visa::query()
            ->active()
            ->featured()
            ->with(['country', 'category'])
            ->orderBy('sort_order')
            ->take($limit)
            ->get();
    }

    /**
     * Get popular visas for homepage.
     *
     * @return Collection<int, Visa>
     */
    public static function getPopularVisas(int $limit = 8): Collection
    {
        return Visa::query()
            ->active()
            ->popular()
            ->with(['country', 'category'])
            ->orderBy('sort_order')
            ->take($limit)
            ->get();
    }

    /**
     * Get popular countries for homepage.
     *
     * @return Collection<int, VisaCountry>
     */
    public static function getPopularCountries(int $limit = 10): Collection
    {
        return VisaCountry::query()
            ->active()
            ->popular()
            ->withCount('visas')
            ->orderBy('sort_order')
            ->take($limit)
            ->get();
    }

    /**
     * Get all active countries.
     *
     * @return Collection<int, VisaCountry>
     */
    public static function getActiveCountries(): Collection
    {
        return VisaCountry::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get all active categories.
     *
     * @return Collection<int, VisaCategory>
     */
    public static function getActiveCategories(): Collection
    {
        return VisaCategory::query()
            ->active()
            ->withCount('visas')
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Search visas with filters.
     */
    public static function searchVisas(): LengthAwarePaginator
    {
        $query = Visa::query()
            ->active()
            ->with(['country', 'category']);

        // Filter by country
        if (request('country')) {
            $query->where('country_id', request('country'));
        }

        // Filter by category
        if (request('category')) {
            $query->where('category_id', request('category'));
        }

        // Filter by entry type
        if (request('entry_type')) {
            $query->where('entry_type', request('entry_type'));
        }

        // Filter by processing time
        if (request('processing')) {
            $days = (int) request('processing');
            $query->where('processing_days_max', '<=', $days);
        }

        // Search by name
        if (request('q')) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.request('q').'%')
                    ->orWhereHas('country', fn ($cq) => $cq->where('name', 'like', '%'.request('q').'%'));
            });
        }

        // Sort
        $sort = request('sort', 'popular');
        if ($sort === 'price_low') {
            $query->orderByRaw('(base_price + service_fee + embassy_fee) ASC');
        } elseif ($sort === 'price_high') {
            $query->orderByRaw('(base_price + service_fee + embassy_fee) DESC');
        } elseif ($sort === 'fastest') {
            $query->orderBy('processing_days_min', 'asc');
        } elseif ($sort === 'popular') {
            $query->orderBy('is_popular', 'desc')->orderBy('sort_order');
        } else {
            $query->latest();
        }

        return $query->paginate(12)->withQueryString();
    }

    /**
     * Get visa by slug with all related data.
     */
    public static function getVisaBySlug(string $slug): ?Visa
    {
        return Visa::query()
            ->where('slug', $slug)
            ->active()
            ->with([
                'country',
                'category',
                'requirements' => fn ($q) => $q->active()->orderBy('sort_order'),
                'fees' => fn ($q) => $q->active()->orderBy('sort_order'),
                'processingTimes' => fn ($q) => $q->active()->orderBy('sort_order'),
                'faqs' => fn ($q) => $q->active()->orderBy('sort_order'),
                'checklists' => fn ($q) => $q->active()->with(['items' => fn ($iq) => $iq->orderBy('sort_order')]),
            ])
            ->first();
    }

    /**
     * Get visas for a country.
     *
     * @return Collection<int, Visa>
     */
    public static function getVisasByCountry(VisaCountry $country): Collection
    {
        return Visa::query()
            ->where('country_id', $country->id)
            ->active()
            ->with(['category'])
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get related visas.
     *
     * @return Collection<int, Visa>
     */
    public static function getRelatedVisas(Visa $visa, int $limit = 4): Collection
    {
        return Visa::query()
            ->active()
            ->where('id', '!=', $visa->id)
            ->where(function ($q) use ($visa) {
                $q->where('country_id', $visa->country_id)
                    ->orWhere('category_id', $visa->category_id);
            })
            ->with(['country', 'category'])
            ->take($limit)
            ->get();
    }
}
