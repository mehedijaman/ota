<?php

namespace Modules\Hotel\Services;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelRate;
use Modules\Hotel\Models\HotelRoom;

class HotelSearchService
{
    public function search(array $filters): LengthAwarePaginator
    {
        $query = Hotel::query()
            ->with(['city', 'country'])
            ->active();

        // Location filter
        if (! empty($filters['city_id'])) {
            $query->where('city_id', $filters['city_id']);
        } elseif (! empty($filters['country_id'])) {
            $query->where('country_id', $filters['country_id']);
        } elseif (! empty($filters['destination'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['destination']}%")
                    ->orWhereHas('city', fn ($cq) => $cq->where('name', 'like', "%{$filters['destination']}%"));
            });
        }

        // Star rating filter
        if (! empty($filters['star_rating'])) {
            $query->where('star_rating', $filters['star_rating']);
        }

        if (! empty($filters['min_stars'])) {
            $query->where('star_rating', '>=', $filters['min_stars']);
        }

        // Property type filter
        if (! empty($filters['property_type'])) {
            $query->where('property_type', $filters['property_type']);
        }

        // Amenities filter
        if (! empty($filters['amenities'])) {
            $query->whereHas('amenities', function ($q) use ($filters) {
                $q->whereIn('hotel_amenities.id', $filters['amenities']);
            });
        }

        // Price range filter - requires availability check
        if (! empty($filters['check_in']) && ! empty($filters['check_out'])) {
            $availableHotelIds = $this->getAvailableHotelIds(
                $filters['check_in'],
                $filters['check_out'],
                $filters['rooms'] ?? 1,
                $filters['adults'] ?? 2,
                $filters['children'] ?? 0
            );

            $query->whereIn('id', $availableHotelIds);
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'recommended';
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('min_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('min_price', 'desc');
                break;
            case 'rating':
                $query->orderByDesc('avg_rating');
                break;
            case 'reviews':
                $query->orderByDesc('reviews_count');
                break;
            default:
                $query->orderByDesc('is_recommended')
                    ->orderByDesc('is_featured')
                    ->orderByDesc('avg_rating');
        }

        return $query->paginate($filters['per_page'] ?? 15)->withQueryString();
    }

    public function getAvailableHotelIds(
        string $checkIn,
        string $checkOut,
        int $rooms = 1,
        int $adults = 2,
        int $children = 0
    ): array {
        $dates = CarbonPeriod::create($checkIn, Carbon::parse($checkOut)->subDay());
        $dateStrings = collect($dates)->map(fn ($date) => $date->format('Y-m-d'))->toArray();

        // Get hotels with available rooms for all dates
        return HotelRoom::query()
            ->active()
            ->where('max_adults', '>=', $adults)
            ->whereHas('inventories', function ($q) use ($dateStrings, $rooms) {
                $q->whereIn('date', $dateStrings)
                    ->where('available', '>=', $rooms)
                    ->groupBy('room_id')
                    ->havingRaw('COUNT(DISTINCT date) = ?', [count($dateStrings)]);
            })
            ->pluck('hotel_id')
            ->unique()
            ->toArray();
    }

    public function checkAvailability(
        int $hotelId,
        string $checkIn,
        string $checkOut,
        int $rooms = 1,
        int $adults = 2,
        int $children = 0
    ): array {
        $dates = CarbonPeriod::create($checkIn, Carbon::parse($checkOut)->subDay());
        $dateStrings = collect($dates)->map(fn ($date) => $date->format('Y-m-d'))->toArray();

        // Get available rooms
        $availableRooms = HotelRoom::query()
            ->where('hotel_id', $hotelId)
            ->active()
            ->where('max_adults', '>=', $adults)
            ->with(['roomType', 'amenities'])
            ->get()
            ->filter(function ($room) use ($dateStrings, $rooms) {
                // Check inventory for all dates
                $inventories = $room->inventories()
                    ->whereIn('date', $dateStrings)
                    ->get();

                if ($inventories->count() !== count($dateStrings)) {
                    return false;
                }

                return $inventories->min('available') >= $rooms;
            });

        // Get rates for available rooms
        $result = [];
        foreach ($availableRooms as $room) {
            $rates = HotelRate::query()
                ->where('room_id', $room->id)
                ->whereIn('date', $dateStrings)
                ->available()
                ->get();

            if ($rates->count() !== count($dateStrings)) {
                continue;
            }

            $totalRate = $rates->sum('selling_rate') ?: $rates->sum('base_rate');
            $avgRate = $totalRate / count($dateStrings);

            $result[] = [
                'room' => $room,
                'nights' => count($dateStrings),
                'total_rate' => $totalRate,
                'avg_rate' => round($avgRate, 2),
                'daily_rates' => $rates->pluck('selling_rate', 'date')->toArray(),
            ];
        }

        // Sort by price
        usort($result, fn ($a, $b) => $a['total_rate'] <=> $b['total_rate']);

        return $result;
    }

    public function getFeaturedHotels(int $limit = 8): Collection
    {
        return Hotel::query()
            ->with(['city', 'country'])
            ->active()
            ->featured()
            ->orderByDesc('avg_rating')
            ->limit($limit)
            ->get();
    }

    public function getPopularDestinations(int $limit = 6): array
    {
        return Hotel::query()
            ->select('city_id')
            ->selectRaw('COUNT(*) as hotels_count')
            ->active()
            ->groupBy('city_id')
            ->orderByDesc('hotels_count')
            ->limit($limit)
            ->with('city.country')
            ->get()
            ->map(fn ($item) => [
                'city' => $item->city,
                'hotels_count' => $item->hotels_count,
            ])
            ->toArray();
    }
}
