<?php

namespace Modules\Flight\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\Flight\DTOs\FlightSearchRequest;
use Modules\Flight\DTOs\FlightSearchResult;
use Modules\Flight\Models\FlightAirport;
use Modules\Flight\Models\FlightSearchLog;

class FlightSearchService
{
    protected int $cacheTtl = 900; // 15 minutes

    /**
     * Search for flights.
     */
    public function search(FlightSearchRequest $request): Collection
    {
        $startTime = microtime(true);

        // Check cache first
        $cacheKey = "flight_search_{$request->getCacheKey()}";
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            // Get results from suppliers (mock for now)
            $results = $this->searchFromSuppliers($request);

            // Filter and sort results
            $results = $this->filterResults($results, $request);
            $results = $this->sortResults($results);

            // Cache results
            Cache::put($cacheKey, $results, $this->cacheTtl);

            // Log the search
            $this->logSearch($request, $results, $startTime);

            return $results;

        } catch (\Exception $e) {
            Log::error('Flight search error', [
                'request' => $request->toArray(),
                'error' => $e->getMessage(),
            ]);

            return collect();
        }
    }

    /**
     * Search from all active suppliers.
     */
    protected function searchFromSuppliers(FlightSearchRequest $request): Collection
    {
        // This would integrate with supplier adapters
        // For now, return mock data
        return $this->getMockResults($request);
    }

    /**
     * Filter search results.
     */
    public function filterResults(Collection $results, FlightSearchRequest $request): Collection
    {
        return $results
            ->when($request->directOnly, fn ($c) => $c->filter(fn ($r) => $r->isDirect()))
            ->when($request->preferredAirline, fn ($c, $airline) => $c->filter(fn ($r) => $r->airlineCode === $airline))
            ->when($request->maxStops !== null, fn ($c) => $c->filter(fn ($r) => $r->stops <= $request->maxStops));
    }

    /**
     * Sort search results.
     */
    public function sortResults(Collection $results, string $sortBy = 'price', string $sortOrder = 'asc'): Collection
    {
        return match ($sortBy) {
            'price' => $results->sortBy('totalPrice', SORT_NUMERIC, $sortOrder === 'desc'),
            'duration' => $results->sortBy('durationMinutes', SORT_NUMERIC, $sortOrder === 'desc'),
            'departure' => $results->sortBy('departureDateTime', SORT_STRING, $sortOrder === 'desc'),
            'arrival' => $results->sortBy('arrivalDateTime', SORT_STRING, $sortOrder === 'desc'),
            'airline' => $results->sortBy('airlineName', SORT_STRING, $sortOrder === 'desc'),
            default => $results->sortBy('totalPrice'),
        };
    }

    /**
     * Get airport suggestions.
     */
    public function searchAirports(string $query, int $limit = 10): Collection
    {
        return FlightAirport::query()
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('iata_code', 'like', "{$query}%")
                    ->orWhere('name', 'like', "%{$query}%")
                    ->orWhere('city', 'like', "%{$query}%");
            })
            ->orderByDesc('is_popular')
            ->orderByDesc('search_priority')
            ->limit($limit)
            ->get(['id', 'iata_code', 'name', 'city', 'country_id']);
    }

    /**
     * Log search for analytics.
     */
    protected function logSearch(FlightSearchRequest $request, Collection $results, float $startTime): void
    {
        $responseTime = (int) ((microtime(true) - $startTime) * 1000);

        FlightSearchLog::create([
            'session_id' => session()->getId(),
            'customer_id' => $request->customerId,
            'agent_id' => $request->agentId,
            'trip_type' => $request->tripType,
            'origin_code' => $request->originCode,
            'destination_code' => $request->destinationCode,
            'departure_date' => $request->departureDate,
            'return_date' => $request->returnDate,
            'adults' => $request->adults,
            'children' => $request->children,
            'infants' => $request->infants,
            'cabin_class' => $request->cabinClass,
            'direct_only' => $request->directOnly,
            'results_count' => $results->count(),
            'lowest_price' => $results->min('totalPrice'),
            'highest_price' => $results->max('totalPrice'),
            'currency_code' => $request->currency,
            'response_time_ms' => $responseTime,
            'source' => $request->source,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get mock results for development.
     */
    protected function getMockResults(FlightSearchRequest $request): Collection
    {
        $results = [];

        $airlines = [
            ['code' => 'EK', 'name' => 'Emirates'],
            ['code' => 'QR', 'name' => 'Qatar Airways'],
            ['code' => 'SQ', 'name' => 'Singapore Airlines'],
            ['code' => 'BG', 'name' => 'Biman Bangladesh Airlines'],
        ];

        foreach ($airlines as $index => $airline) {
            $basePrice = rand(15000, 45000);
            $taxes = round($basePrice * 0.1, 2);

            $results[] = FlightSearchResult::fromArray([
                'supplier_code' => 'DEMO',
                'supplier_id' => 'demo_'.($index + 1),
                'offer_id' => 'offer_'.uniqid(),
                'airline_code' => $airline['code'],
                'airline_name' => $airline['name'],
                'flight_number' => rand(100, 999),
                'origin_code' => $request->originCode,
                'origin_name' => $request->originCode.' Airport',
                'destination_code' => $request->destinationCode,
                'destination_name' => $request->destinationCode.' Airport',
                'departure_datetime' => $request->departureDate.' '.sprintf('%02d:%02d', rand(6, 22), rand(0, 59)),
                'arrival_datetime' => $request->departureDate.' '.sprintf('%02d:%02d', rand(8, 23), rand(0, 59)),
                'duration_minutes' => rand(120, 480),
                'stops' => rand(0, 2),
                'stopover_airports' => [],
                'cabin_class' => $request->cabinClass,
                'fare_class' => 'Y',
                'fare_basis' => 'YOWBD',
                'base_price' => $basePrice,
                'taxes' => $taxes,
                'fees' => 0,
                'total_price' => $basePrice + $taxes,
                'currency' => $request->currency ?? 'BDT',
                'available_seats' => rand(1, 9),
                'baggage_allowance' => '2PC x 23kg',
                'is_refundable' => rand(0, 1) === 1,
                'is_changeable' => true,
            ]);
        }

        return collect($results);
    }
}
