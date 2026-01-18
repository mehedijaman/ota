<?php

namespace Modules\Flight\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Flight\DTOs\FlightSearchRequest;
use Modules\Flight\Http\Requests\FlightSearchValidate;
use Modules\Flight\Services\FlightSearchService;

class FlightSearchController extends Controller
{
    public function __construct(
        protected FlightSearchService $searchService
    ) {}

    /**
     * Search flights via API.
     */
    public function search(FlightSearchValidate $request): JsonResponse
    {
        $searchRequest = FlightSearchRequest::fromArray($request->validated());

        $results = $this->searchService->search($searchRequest);

        return response()->json([
            'success' => true,
            'data' => [
                'results' => $results->values(),
                'count' => $results->count(),
                'search' => $searchRequest->toArray(),
            ],
        ]);
    }

    /**
     * Search airports for autocomplete.
     */
    public function searchAirports(): JsonResponse
    {
        $query = request('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $airports = $this->searchService->searchAirports($query);

        return response()->json($airports);
    }

    /**
     * Get popular airports.
     */
    public function popularAirports(): JsonResponse
    {
        $airports = $this->searchService->searchAirports('', 20);

        return response()->json($airports);
    }

    /**
     * Apply filters to search results.
     */
    public function filter(): JsonResponse
    {
        $searchRequest = FlightSearchRequest::fromArray(request()->all());
        $results = $this->searchService->search($searchRequest);

        // Apply additional filters
        $sortBy = request('sort_by', 'price');
        $sortOrder = request('sort_order', 'asc');
        $results = $this->searchService->sortResults($results, $sortBy, $sortOrder);

        // Filter by criteria
        if (request('max_price')) {
            $results = $results->filter(fn ($r) => $r->totalPrice <= request('max_price'));
        }

        if (request('airlines')) {
            $airlines = (array) request('airlines');
            $results = $results->filter(fn ($r) => in_array($r->airlineCode, $airlines));
        }

        if (request('max_duration')) {
            $results = $results->filter(fn ($r) => $r->durationMinutes <= request('max_duration'));
        }

        return response()->json([
            'success' => true,
            'data' => [
                'results' => $results->values(),
                'count' => $results->count(),
            ],
        ]);
    }
}
