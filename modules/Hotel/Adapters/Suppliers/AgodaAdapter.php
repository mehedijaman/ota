<?php

namespace Modules\Hotel\Adapters\Suppliers;

use Modules\Hotel\Adapters\BaseSupplierAdapter;
use Modules\Hotel\DTOs\BookingRequest;
use Modules\Hotel\DTOs\BookingResult;
use Modules\Hotel\DTOs\HotelAvailabilityRequest;
use Modules\Hotel\DTOs\HotelAvailabilityResult;
use Modules\Hotel\DTOs\HotelSearchRequest;
use Modules\Hotel\DTOs\HotelSearchResult;

/**
 * Agoda Partner API Adapter.
 *
 * Integrates with Agoda Partner API for hotel search and booking.
 *
 * @see https://partners.agoda.com/en-us/partner-api
 */
class AgodaAdapter extends BaseSupplierAdapter
{
    protected string $apiVersion = 'v2';

    public function getSupplierCode(): string
    {
        return 'agoda';
    }

    protected function getBaseUrl(): string
    {
        return $this->environment === 'production'
            ? 'https://api.agoda.com/api/'.$this->apiVersion
            : 'https://sandbox-api.agoda.com/api/'.$this->apiVersion;
    }

    protected function getAuthHeaders(): array
    {
        $apiKey = $this->credential?->api_key ?? '';
        $siteId = $this->credential?->additional_config['site_id'] ?? '';

        return [
            'Authorization' => 'Bearer '.$apiKey,
            'X-Site-ID' => $siteId,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    public function searchHotels(HotelSearchRequest $request): array
    {
        if (! $this->isConfigured()) {
            return [];
        }

        $payload = [
            'criteria' => [
                'checkInDate' => $request->checkIn,
                'checkOutDate' => $request->checkOut,
                'rooms' => $request->rooms,
                'adults' => $request->adults,
                'children' => $request->children,
                'childAges' => $request->childAges,
                'cityId' => $this->getCityId($request->destination),
                'language' => 'en-us',
                'currency' => $request->currency,
            ],
            'pagination' => [
                'pageSize' => $request->limit,
                'pageNumber' => ($request->offset / max(1, $request->limit)) + 1,
            ],
            'sorting' => [
                'sortBy' => $this->mapSortBy($request->sortBy),
                'sortOrder' => 'desc',
            ],
        ];

        // Add star rating filter
        if ($request->minStars || $request->maxStars) {
            $payload['filters']['starRating'] = [
                'min' => $request->minStars ?? 1,
                'max' => $request->maxStars ?? 5,
            ];
        }

        // Add price filter
        if ($request->minPrice || $request->maxPrice) {
            $payload['filters']['priceRange'] = [
                'min' => $request->minPrice ?? 0,
                'max' => $request->maxPrice ?? 999999,
                'currency' => $request->currency,
            ];
        }

        $response = $this->post('/hotels/search', $payload);

        if (empty($response['hotels'])) {
            return [];
        }

        return array_map(
            fn ($hotel) => $this->mapHotelToResult($hotel, $request->currency),
            $response['hotels']
        );
    }

    public function getHotelDetails(string $supplierHotelId): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $response = $this->get("/hotels/{$supplierHotelId}", [
            'language' => 'en-us',
        ]);

        return $response['hotel'] ?? null;
    }

    public function checkAvailability(HotelAvailabilityRequest $request): HotelAvailabilityResult
    {
        if (! $this->isConfigured()) {
            return $this->unavailableResult($request->supplierHotelId);
        }

        $payload = [
            'hotelId' => $request->supplierHotelId,
            'checkInDate' => $request->checkIn,
            'checkOutDate' => $request->checkOut,
            'rooms' => $request->rooms,
            'adults' => $request->adults,
            'children' => $request->children,
            'childAges' => $request->childAges,
            'currency' => $request->currency,
            'language' => 'en-us',
        ];

        $response = $this->post('/hotels/availability', $payload);

        if (empty($response['rooms'])) {
            return $this->unavailableResult($request->supplierHotelId);
        }

        $rooms = $this->mapRooms($response['rooms'], $request->currency);

        return new HotelAvailabilityResult(
            supplierCode: 'agoda',
            supplierHotelId: $request->supplierHotelId,
            isAvailable: count($rooms) > 0,
            rooms: $rooms,
            currency: $response['currency'] ?? $request->currency,
            cancellationPolicy: $response['cancellationPolicy'] ?? null,
            rawData: $response,
        );
    }

    public function createBooking(BookingRequest $request): BookingResult
    {
        if (! $this->isConfigured()) {
            return $this->failedBookingResult('Agoda adapter not configured');
        }

        $payload = [
            'hotelId' => $request->supplierHotelId,
            'roomId' => $request->supplierRoomId,
            'rateKey' => $request->supplierRateKey,
            'checkInDate' => $request->checkIn,
            'checkOutDate' => $request->checkOut,
            'rooms' => $request->rooms,
            'adults' => $request->adults,
            'children' => $request->children,
            'contact' => [
                'title' => $request->contactTitle,
                'firstName' => $request->contactFirstName,
                'lastName' => $request->contactLastName,
                'email' => $request->contactEmail,
                'phone' => $request->contactPhone,
                'country' => $request->contactCountry ?? 'BD',
            ],
            'guests' => $this->buildGuestList($request),
            'specialRequests' => $request->specialRequests,
            'partnerReference' => $request->localBookingId ?? uniqid('AGO-'),
            'currency' => $request->currency,
        ];

        $response = $this->post('/bookings', $payload);

        if (empty($response['booking'])) {
            return $this->failedBookingResult(
                $response['error']['message'] ?? 'Booking failed',
                $response['errors'] ?? []
            );
        }

        $booking = $response['booking'];

        return new BookingResult(
            supplierCode: 'agoda',
            status: $this->mapBookingStatus($booking['status'] ?? 'UNKNOWN'),
            supplierBookingId: $booking['bookingId'] ?? null,
            supplierConfirmationNumber: $booking['confirmationNumber'] ?? null,
            message: 'Booking created successfully',
            totalAmount: $booking['totalAmount'] ?? null,
            currency: $booking['currency'] ?? $request->currency,
            cancellationDeadline: $booking['freeCancellationDeadline'] ?? null,
            cancellationPolicy: $booking['cancellationPolicy'] ?? null,
            rawData: $response,
        );
    }

    public function confirmBooking(string $supplierBookingId): BookingResult
    {
        // Agoda bookings are typically confirmed immediately
        return $this->getBookingStatus($supplierBookingId);
    }

    public function cancelBooking(string $supplierBookingId, string $reason): BookingResult
    {
        if (! $this->isConfigured()) {
            return $this->failedBookingResult('Agoda adapter not configured');
        }

        $response = $this->post("/bookings/{$supplierBookingId}/cancel", [
            'reason' => $reason,
        ]);

        if (isset($response['error'])) {
            return $this->failedBookingResult($response['error']['message'] ?? 'Cancellation failed');
        }

        return new BookingResult(
            supplierCode: 'agoda',
            status: BookingResult::STATUS_CANCELLED,
            supplierBookingId: $supplierBookingId,
            message: 'Booking cancelled successfully',
            rawData: $response,
        );
    }

    public function getBookingStatus(string $supplierBookingId): BookingResult
    {
        if (! $this->isConfigured()) {
            return $this->failedBookingResult('Agoda adapter not configured');
        }

        $response = $this->get("/bookings/{$supplierBookingId}");

        if (empty($response['booking'])) {
            return $this->failedBookingResult('Booking not found');
        }

        $booking = $response['booking'];

        return new BookingResult(
            supplierCode: 'agoda',
            status: $this->mapBookingStatus($booking['status'] ?? 'UNKNOWN'),
            supplierBookingId: $booking['bookingId'] ?? null,
            supplierConfirmationNumber: $booking['confirmationNumber'] ?? null,
            totalAmount: $booking['totalAmount'] ?? null,
            currency: $booking['currency'] ?? null,
            rawData: $response,
        );
    }

    public function ping(): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        $response = $this->get('/status');

        return isset($response['status']) && $response['status'] === 'ok';
    }

    public function syncHotels(?string $destinationCode = null): int
    {
        if (! $this->isConfigured()) {
            return 0;
        }

        $count = 0;

        try {
            $payload = [
                'language' => 'en-us',
                'pageSize' => 1000,
                'pageNumber' => 1,
            ];

            if ($destinationCode) {
                $payload['cityId'] = $destinationCode;
            }

            $response = $this->post('/content/hotels', $payload);

            $count = count($response['hotels'] ?? []);

            $this->logSync('hotels', $count);

        } catch (\Exception $e) {
            $this->logSync('hotels', 0, $e->getMessage());
        }

        return $count;
    }

    protected function mapHotelToResult(array $hotel, string $currency): HotelSearchResult
    {
        return new HotelSearchResult(
            supplierCode: 'agoda',
            supplierHotelId: (string) ($hotel['hotelId'] ?? ''),
            name: $hotel['hotelName'] ?? '',
            starRating: (int) ($hotel['starRating'] ?? 3),
            address: $hotel['address']['addressLine'] ?? '',
            city: $hotel['address']['city'] ?? '',
            country: $hotel['address']['country'] ?? '',
            latitude: (float) ($hotel['geoInfo']['latitude'] ?? 0),
            longitude: (float) ($hotel['geoInfo']['longitude'] ?? 0),
            imageUrl: $hotel['images'][0]['url'] ?? null,
            images: array_column($hotel['images'] ?? [], 'url'),
            amenities: array_column($hotel['facilities'] ?? [], 'name'),
            minRate: (float) ($hotel['pricing']['lowestPrice'] ?? 0),
            maxRate: (float) ($hotel['pricing']['highestPrice'] ?? 0),
            currency: $currency,
            reviewScore: (float) ($hotel['review']['score'] ?? null),
            reviewCount: (int) ($hotel['review']['count'] ?? 0),
            isAvailable: ($hotel['availability'] ?? 'available') === 'available',
            rooms: [],
            rawData: $hotel,
        );
    }

    protected function mapRooms(array $rooms, string $currency): array
    {
        $mapped = [];

        foreach ($rooms as $room) {
            $mapped[] = [
                'room_id' => $room['roomId'] ?? '',
                'room_name' => $room['roomName'] ?? '',
                'max_occupancy' => ($room['maxAdults'] ?? 2) + ($room['maxChildren'] ?? 0),
                'rate_key' => $room['rateKey'] ?? '',
                'board_name' => $room['mealPlan']['name'] ?? 'Room Only',
                'nightly_rate' => (float) ($room['nightlyRate'] ?? 0),
                'total_rate' => (float) ($room['totalRate'] ?? 0),
                'currency' => $currency,
                'cancellation_policy' => $room['cancellationPolicy']['description'] ?? null,
                'is_refundable' => $room['cancellationPolicy']['isRefundable'] ?? true,
                'available_rooms' => $room['availableRooms'] ?? 1,
            ];
        }

        return $mapped;
    }

    protected function buildGuestList(BookingRequest $request): array
    {
        $guests = [];

        // Primary guest
        $guests[] = [
            'title' => $request->contactTitle,
            'firstName' => $request->contactFirstName,
            'lastName' => $request->contactLastName,
            'isPrimary' => true,
        ];

        // Additional guests
        foreach ($request->guests as $guest) {
            $guests[] = [
                'title' => $guest['title'] ?? 'Mr',
                'firstName' => $guest['first_name'] ?? 'Guest',
                'lastName' => $guest['last_name'] ?? '',
                'isPrimary' => false,
            ];
        }

        return $guests;
    }

    protected function mapBookingStatus(string $status): string
    {
        return match (strtoupper($status)) {
            'CONFIRMED' => BookingResult::STATUS_CONFIRMED,
            'CANCELLED' => BookingResult::STATUS_CANCELLED,
            'PENDING' => BookingResult::STATUS_PENDING,
            'ON_REQUEST' => BookingResult::STATUS_ON_REQUEST,
            default => BookingResult::STATUS_PENDING,
        };
    }

    protected function mapSortBy(?string $sortBy): string
    {
        return match ($sortBy) {
            'price_asc' => 'PriceLowToHigh',
            'price_desc' => 'PriceHighToLow',
            'rating' => 'GuestRating',
            'stars' => 'StarRating',
            'popularity' => 'Popularity',
            default => 'Recommended',
        };
    }

    protected function getCityId(string $destination): string
    {
        // Map common destinations to Agoda city IDs
        // In production, this would look up from a database
        $cities = [
            'dhaka' => '17182',
            'chittagong' => '15147',
            'cox\'s bazar' => '17193',
            'sylhet' => '17196',
            'khulna' => '17191',
            'rajshahi' => '17194',
            'rangpur' => '17195',
        ];

        return $cities[strtolower($destination)] ?? $destination;
    }
}
