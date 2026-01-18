<?php

namespace Modules\Flight\Adapters\Contracts;

use Illuminate\Support\Collection;
use Modules\Flight\DTOs\FlightBookingRequest;
use Modules\Flight\DTOs\FlightBookingResult;
use Modules\Flight\DTOs\FlightSearchRequest;

interface FlightSupplierAdapterInterface
{
    /**
     * Get the supplier code.
     */
    public function getCode(): string;

    /**
     * Get the supplier name.
     */
    public function getName(): string;

    /**
     * Check if the supplier is available.
     */
    public function isAvailable(): bool;

    /**
     * Search for flights.
     */
    public function search(FlightSearchRequest $request): Collection;

    /**
     * Get flight details/pricing.
     */
    public function getFlightDetails(string $offerId): ?array;

    /**
     * Create a booking.
     */
    public function book(FlightBookingRequest $request): FlightBookingResult;

    /**
     * Cancel a booking.
     */
    public function cancel(string $supplierBookingId, ?string $reason = null): FlightBookingResult;

    /**
     * Issue tickets.
     */
    public function issueTicket(string $supplierBookingId): FlightBookingResult;

    /**
     * Void tickets.
     */
    public function voidTicket(string $supplierBookingId): FlightBookingResult;

    /**
     * Get booking status.
     */
    public function getBookingStatus(string $supplierBookingId): array;

    /**
     * Get fare rules.
     */
    public function getFareRules(string $offerId): array;

    /**
     * Get seat map.
     */
    public function getSeatMap(string $flightNumber, string $date): array;
}
