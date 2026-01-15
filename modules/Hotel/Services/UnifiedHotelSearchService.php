<?php

namespace Modules\Hotel\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\Hotel\Adapters\Contracts\HotelSupplierAdapterInterface;
use Modules\Hotel\Adapters\SupplierAdapterFactory;
use Modules\Hotel\DTOs\BookingRequest;
use Modules\Hotel\DTOs\BookingResult;
use Modules\Hotel\DTOs\HotelAvailabilityRequest;
use Modules\Hotel\DTOs\HotelAvailabilityResult;
use Modules\Hotel\DTOs\HotelSearchRequest;
use Modules\Hotel\DTOs\HotelSearchResult;

/**
 * Unified Hotel Search Service.
 *
 * Aggregates hotel search results from multiple suppliers (local DB + APIs)
 * and provides a unified interface for searching, availability checks, and bookings.
 */
class UnifiedHotelSearchService
{
    protected int $cacheMinutes = 15;

    protected bool $cacheEnabled = true;

    /**
     * Search hotels across all active suppliers.
     *
     * @return HotelSearchResult[]
     */
    public function search(HotelSearchRequest $request): array
    {
        $cacheKey = $this->getCacheKey('search', $request->toArray());

        if ($this->cacheEnabled && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $results = [];
        $adapters = SupplierAdapterFactory::getAllActive();

        foreach ($adapters as $adapter) {
            try {
                $supplierResults = $adapter->searchHotels($request);
                $results = array_merge($results, $supplierResults);
            } catch (\Exception $e) {
                Log::warning("Supplier search failed: {$adapter->getSupplierCode()}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Sort and deduplicate
        $results = $this->processResults($results, $request);

        if ($this->cacheEnabled) {
            Cache::put($cacheKey, $results, now()->addMinutes($this->cacheMinutes));
        }

        return $results;
    }

    /**
     * Search a specific supplier only.
     *
     * @return HotelSearchResult[]
     */
    public function searchSupplier(string $supplierCode, HotelSearchRequest $request): array
    {
        $adapter = SupplierAdapterFactory::makeByType($supplierCode);

        if (! $adapter) {
            return [];
        }

        return $adapter->searchHotels($request);
    }

    /**
     * Check availability for a hotel from a specific supplier.
     */
    public function checkAvailability(HotelAvailabilityRequest $request): HotelAvailabilityResult
    {
        $adapter = SupplierAdapterFactory::makeByType($request->supplierCode);

        if (! $adapter) {
            return new HotelAvailabilityResult(
                supplierCode: $request->supplierCode,
                supplierHotelId: $request->supplierHotelId,
                isAvailable: false,
            );
        }

        $cacheKey = $this->getCacheKey('availability', $request->toArray());

        if ($this->cacheEnabled && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $result = $adapter->checkAvailability($request);

        if ($this->cacheEnabled && $result->isAvailable) {
            Cache::put($cacheKey, $result, now()->addMinutes(5)); // Shorter cache for availability
        }

        return $result;
    }

    /**
     * Create a booking with the appropriate supplier.
     */
    public function createBooking(BookingRequest $request): BookingResult
    {
        $adapter = SupplierAdapterFactory::makeByType($request->supplierCode);

        if (! $adapter) {
            return new BookingResult(
                supplierCode: $request->supplierCode,
                status: BookingResult::STATUS_FAILED,
                message: "Supplier not found: {$request->supplierCode}",
            );
        }

        return $adapter->createBooking($request);
    }

    /**
     * Confirm a booking with the supplier.
     */
    public function confirmBooking(string $supplierCode, string $supplierBookingId): BookingResult
    {
        $adapter = SupplierAdapterFactory::makeByType($supplierCode);

        if (! $adapter) {
            return new BookingResult(
                supplierCode: $supplierCode,
                status: BookingResult::STATUS_FAILED,
                message: "Supplier not found: {$supplierCode}",
            );
        }

        return $adapter->confirmBooking($supplierBookingId);
    }

    /**
     * Cancel a booking with the supplier.
     */
    public function cancelBooking(string $supplierCode, string $supplierBookingId, string $reason): BookingResult
    {
        $adapter = SupplierAdapterFactory::makeByType($supplierCode);

        if (! $adapter) {
            return new BookingResult(
                supplierCode: $supplierCode,
                status: BookingResult::STATUS_FAILED,
                message: "Supplier not found: {$supplierCode}",
            );
        }

        return $adapter->cancelBooking($supplierBookingId, $reason);
    }

    /**
     * Get booking status from supplier.
     */
    public function getBookingStatus(string $supplierCode, string $supplierBookingId): BookingResult
    {
        $adapter = SupplierAdapterFactory::makeByType($supplierCode);

        if (! $adapter) {
            return new BookingResult(
                supplierCode: $supplierCode,
                status: BookingResult::STATUS_FAILED,
                message: "Supplier not found: {$supplierCode}",
            );
        }

        return $adapter->getBookingStatus($supplierBookingId);
    }

    /**
     * Get all available suppliers.
     *
     * @return HotelSupplierAdapterInterface[]
     */
    public function getActiveSuppliers(): array
    {
        return SupplierAdapterFactory::getAllActive();
    }

    /**
     * Check health of all suppliers.
     */
    public function healthCheck(): array
    {
        $status = [];
        $adapters = SupplierAdapterFactory::getAllActive();

        foreach ($adapters as $adapter) {
            $status[$adapter->getSupplierCode()] = [
                'configured' => $adapter->isConfigured(),
                'healthy' => $adapter->ping(),
            ];
        }

        return $status;
    }

    /**
     * Process and sort search results.
     *
     * @param  HotelSearchResult[]  $results
     * @return HotelSearchResult[]
     */
    protected function processResults(array $results, HotelSearchRequest $request): array
    {
        // Convert to collection for easier manipulation
        $collection = collect($results);

        // Remove duplicates (same hotel from multiple suppliers - keep lowest price)
        $collection = $collection->unique(function (HotelSearchResult $result) {
            // If it's a local hotel, use local_hotel_id, otherwise supplier_hotel_id
            return $result->localHotelId
                ? 'local-'.$result->localHotelId
                : $result->supplierCode.'-'.$result->supplierHotelId;
        });

        // Apply price filters
        if ($request->minPrice) {
            $collection = $collection->where('minRate', '>=', $request->minPrice);
        }
        if ($request->maxPrice) {
            $collection = $collection->where('minRate', '<=', $request->maxPrice);
        }

        // Sort results
        $collection = match ($request->sortBy) {
            'price_asc' => $collection->sortBy('minRate'),
            'price_desc' => $collection->sortByDesc('minRate'),
            'rating' => $collection->sortByDesc('reviewScore'),
            'stars' => $collection->sortByDesc('starRating'),
            'name' => $collection->sortBy('name'),
            default => $collection->sortByDesc('reviewScore'),
        };

        return $collection->values()->toArray();
    }

    /**
     * Generate cache key.
     */
    protected function getCacheKey(string $prefix, array $params): string
    {
        return 'hotel_'.$prefix.'_'.md5(json_encode($params));
    }

    /**
     * Enable or disable caching.
     */
    public function setCacheEnabled(bool $enabled): self
    {
        $this->cacheEnabled = $enabled;

        return $this;
    }

    /**
     * Clear search cache.
     */
    public function clearCache(): void
    {
        Cache::forget('hotel_search_*');
        Cache::forget('hotel_availability_*');
    }
}
