<?php

namespace Modules\Hotel\Adapters\Contracts;

use Modules\Hotel\DTOs\BookingRequest;
use Modules\Hotel\DTOs\BookingResult;
use Modules\Hotel\DTOs\HotelAvailabilityRequest;
use Modules\Hotel\DTOs\HotelAvailabilityResult;
use Modules\Hotel\DTOs\HotelSearchRequest;
use Modules\Hotel\DTOs\HotelSearchResult;

/**
 * Contract for hotel supplier API adapters.
 *
 * All supplier implementations (Hotelbeds, Expedia, etc.) must implement this interface
 * to ensure consistent behavior across different API providers.
 */
interface HotelSupplierAdapterInterface
{
    /**
     * Get the supplier identifier.
     */
    public function getSupplierCode(): string;

    /**
     * Check if the adapter is properly configured and ready to use.
     */
    public function isConfigured(): bool;

    /**
     * Search for hotels matching the given criteria.
     *
     * @return HotelSearchResult[]
     */
    public function searchHotels(HotelSearchRequest $request): array;

    /**
     * Get detailed information about a specific hotel.
     */
    public function getHotelDetails(string $supplierHotelId): ?array;

    /**
     * Check availability and rates for a hotel.
     */
    public function checkAvailability(HotelAvailabilityRequest $request): HotelAvailabilityResult;

    /**
     * Create a booking with the supplier.
     */
    public function createBooking(BookingRequest $request): BookingResult;

    /**
     * Confirm a pending booking.
     */
    public function confirmBooking(string $supplierBookingId): BookingResult;

    /**
     * Cancel an existing booking.
     */
    public function cancelBooking(string $supplierBookingId, string $reason): BookingResult;

    /**
     * Get the current status of a booking.
     */
    public function getBookingStatus(string $supplierBookingId): BookingResult;

    /**
     * Sync hotel static data (hotels, rooms, amenities).
     * Returns the number of hotels synced.
     */
    public function syncHotels(?string $destinationCode = null): int;

    /**
     * Health check for the supplier API.
     */
    public function ping(): bool;
}
