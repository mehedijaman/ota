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
 * Hotelbeds API Adapter.
 *
 * Integrates with Hotelbeds (APItude) API for hotel search and booking.
 *
 * @see https://developer.hotelbeds.com/documentation/hotels/
 */
class HotelbedsAdapter extends BaseSupplierAdapter
{
    protected string $apiVersion = '1.2';

    public function getSupplierCode(): string
    {
        return 'hotelbeds';
    }

    protected function getBaseUrl(): string
    {
        return $this->environment === 'production'
            ? 'https://api.hotelbeds.com/hotel-api/'.$this->apiVersion
            : 'https://api.test.hotelbeds.com/hotel-api/'.$this->apiVersion;
    }

    protected function getAuthHeaders(): array
    {
        $apiKey = $this->credential?->api_key ?? '';
        $secret = $this->credential?->api_secret ?? '';
        $signature = hash('sha256', $apiKey.$secret.time());

        return [
            'Api-key' => $apiKey,
            'X-Signature' => $signature,
            'Accept' => 'application/json',
            'Accept-Encoding' => 'gzip',
        ];
    }

    public function searchHotels(HotelSearchRequest $request): array
    {
        if (! $this->isConfigured()) {
            return [];
        }

        $payload = [
            'stay' => [
                'checkIn' => $request->checkIn,
                'checkOut' => $request->checkOut,
            ],
            'occupancies' => [
                [
                    'rooms' => $request->rooms,
                    'adults' => $request->adults,
                    'children' => $request->children,
                    'paxes' => $this->buildPaxes($request->adults, $request->children, $request->childAges),
                ],
            ],
            'destination' => [
                'code' => $this->getDestinationCode($request->destination),
            ],
            'filter' => [
                'maxHotels' => $request->limit,
            ],
        ];

        // Add star rating filter
        if ($request->minStars) {
            $payload['filter']['minCategory'] = $request->minStars;
        }
        if ($request->maxStars) {
            $payload['filter']['maxCategory'] = $request->maxStars;
        }

        $response = $this->post('/hotels', $payload);

        if (empty($response['hotels']['hotels'])) {
            return [];
        }

        return array_map(
            fn ($hotel) => $this->mapHotelToResult($hotel, $request->currency),
            $response['hotels']['hotels']
        );
    }

    public function getHotelDetails(string $supplierHotelId): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $response = $this->get("/hotels/{$supplierHotelId}/details");

        return $response['hotel'] ?? null;
    }

    public function checkAvailability(HotelAvailabilityRequest $request): HotelAvailabilityResult
    {
        if (! $this->isConfigured()) {
            return $this->unavailableResult($request->supplierHotelId);
        }

        $payload = [
            'stay' => [
                'checkIn' => $request->checkIn,
                'checkOut' => $request->checkOut,
            ],
            'occupancies' => [
                [
                    'rooms' => $request->rooms,
                    'adults' => $request->adults,
                    'children' => $request->children,
                ],
            ],
            'hotels' => [
                'hotel' => [(int) $request->supplierHotelId],
            ],
        ];

        $response = $this->post('/hotels', $payload);

        if (empty($response['hotels']['hotels'])) {
            return $this->unavailableResult($request->supplierHotelId);
        }

        $hotel = $response['hotels']['hotels'][0];
        $rooms = $this->mapRooms($hotel['rooms'] ?? [], $request->currency);

        return new HotelAvailabilityResult(
            supplierCode: 'hotelbeds',
            supplierHotelId: $request->supplierHotelId,
            isAvailable: count($rooms) > 0,
            rooms: $rooms,
            currency: $response['hotels']['currency'] ?? $request->currency,
            rawData: $response,
        );
    }

    public function createBooking(BookingRequest $request): BookingResult
    {
        if (! $this->isConfigured()) {
            return $this->failedBookingResult('Hotelbeds adapter not configured');
        }

        $payload = [
            'holder' => [
                'name' => $request->contactFirstName,
                'surname' => $request->contactLastName,
            ],
            'rooms' => [
                [
                    'rateKey' => $request->supplierRateKey,
                    'paxes' => $this->buildBookingPaxes($request),
                ],
            ],
            'clientReference' => $request->localBookingId ?? uniqid('HB-'),
            'remark' => $request->specialRequests,
            'tolerance' => 2, // Allow 2% price variance
        ];

        $response = $this->post('/bookings', $payload);

        if (empty($response['booking'])) {
            return $this->failedBookingResult(
                $response['error']['message'] ?? 'Booking failed',
                $response['error'] ?? []
            );
        }

        $booking = $response['booking'];

        return new BookingResult(
            supplierCode: 'hotelbeds',
            status: $this->mapBookingStatus($booking['status'] ?? 'UNKNOWN'),
            supplierBookingId: $booking['reference'] ?? null,
            supplierConfirmationNumber: $booking['clientReference'] ?? null,
            message: 'Booking created successfully',
            totalAmount: $booking['totalNet'] ?? null,
            currency: $booking['currency'] ?? $request->currency,
            cancellationDeadline: $booking['hotel']['cancellationDeadline'] ?? null,
            rawData: $response,
        );
    }

    public function confirmBooking(string $supplierBookingId): BookingResult
    {
        // Hotelbeds bookings are confirmed immediately
        return $this->getBookingStatus($supplierBookingId);
    }

    public function cancelBooking(string $supplierBookingId, string $reason): BookingResult
    {
        if (! $this->isConfigured()) {
            return $this->failedBookingResult('Hotelbeds adapter not configured');
        }

        $response = $this->httpClient()->delete("/bookings/{$supplierBookingId}", [
            'cancellationFlag' => 'CANCELLATION',
        ]);

        if ($response->failed()) {
            return $this->failedBookingResult('Cancellation failed');
        }

        $data = $response->json();

        return new BookingResult(
            supplierCode: 'hotelbeds',
            status: BookingResult::STATUS_CANCELLED,
            supplierBookingId: $supplierBookingId,
            message: 'Booking cancelled successfully',
            rawData: $data,
        );
    }

    public function getBookingStatus(string $supplierBookingId): BookingResult
    {
        if (! $this->isConfigured()) {
            return $this->failedBookingResult('Hotelbeds adapter not configured');
        }

        $response = $this->get("/bookings/{$supplierBookingId}");

        if (empty($response['booking'])) {
            return $this->failedBookingResult('Booking not found');
        }

        $booking = $response['booking'];

        return new BookingResult(
            supplierCode: 'hotelbeds',
            status: $this->mapBookingStatus($booking['status'] ?? 'UNKNOWN'),
            supplierBookingId: $booking['reference'] ?? null,
            supplierConfirmationNumber: $booking['clientReference'] ?? null,
            totalAmount: $booking['totalNet'] ?? null,
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

        return ! empty($response);
    }

    public function syncHotels(?string $destinationCode = null): int
    {
        if (! $this->isConfigured()) {
            return 0;
        }

        // Hotelbeds content API for hotel static data sync
        // This would typically be run as a background job
        $count = 0;

        try {
            $payload = [
                'fields' => 'all',
                'language' => 'ENG',
                'from' => 1,
                'to' => 1000,
            ];

            if ($destinationCode) {
                $payload['destinationCode'] = $destinationCode;
            }

            // This is a simplified example - real implementation would paginate
            $response = $this->post('/hotels', $payload);

            $count = count($response['hotels'] ?? []);

            $this->logSync('hotels', $count);

        } catch (\Exception $e) {
            $this->logSync('hotels', 0, $e->getMessage());
        }

        return $count;
    }

    protected function mapHotelToResult(array $hotel, string $currency): HotelSearchResult
    {
        $minRate = PHP_FLOAT_MAX;
        $maxRate = 0;

        foreach ($hotel['rooms'] ?? [] as $room) {
            foreach ($room['rates'] ?? [] as $rate) {
                $rateAmount = (float) ($rate['net'] ?? 0);
                $minRate = min($minRate, $rateAmount);
                $maxRate = max($maxRate, $rateAmount);
            }
        }

        return new HotelSearchResult(
            supplierCode: 'hotelbeds',
            supplierHotelId: (string) ($hotel['code'] ?? ''),
            name: $hotel['name'] ?? '',
            starRating: (int) ($hotel['categoryCode'] ?? 3),
            address: $hotel['address']['content'] ?? '',
            city: $hotel['city']['content'] ?? '',
            country: $hotel['country']['description']['content'] ?? '',
            latitude: (float) ($hotel['latitude'] ?? 0),
            longitude: (float) ($hotel['longitude'] ?? 0),
            imageUrl: $this->buildImageUrl($hotel['images'][0]['path'] ?? null),
            images: array_map(
                fn ($img) => $this->buildImageUrl($img['path'] ?? ''),
                $hotel['images'] ?? []
            ),
            amenities: array_column($hotel['facilities'] ?? [], 'description', 'content'),
            minRate: $minRate === PHP_FLOAT_MAX ? 0 : $minRate,
            maxRate: $maxRate,
            currency: $currency,
            reviewScore: (float) ($hotel['reviews'][0]['rate'] ?? null),
            reviewCount: (int) ($hotel['reviews'][0]['reviewCount'] ?? 0),
            isAvailable: ! empty($hotel['rooms']),
            rooms: $this->mapRooms($hotel['rooms'] ?? [], $currency),
            rawData: $hotel,
        );
    }

    protected function mapRooms(array $rooms, string $currency): array
    {
        $mapped = [];

        foreach ($rooms as $room) {
            foreach ($room['rates'] ?? [] as $rate) {
                $mapped[] = [
                    'room_id' => $room['code'] ?? '',
                    'room_name' => $room['name'] ?? '',
                    'max_occupancy' => ($rate['adults'] ?? 2) + ($rate['children'] ?? 0),
                    'rate_key' => $rate['rateKey'] ?? '',
                    'board_name' => $rate['boardName'] ?? '',
                    'nightly_rate' => (float) ($rate['net'] ?? 0) / max(1, $rate['rooms'] ?? 1),
                    'total_rate' => (float) ($rate['net'] ?? 0),
                    'currency' => $currency,
                    'cancellation_policies' => $rate['cancellationPolicies'] ?? [],
                    'available_rooms' => $rate['allotment'] ?? 1,
                ];
            }
        }

        return $mapped;
    }

    protected function buildPaxes(int $adults, int $children, array $childAges): array
    {
        $paxes = [];

        for ($i = 0; $i < $adults; $i++) {
            $paxes[] = ['type' => 'AD'];
        }

        foreach ($childAges as $age) {
            $paxes[] = ['type' => 'CH', 'age' => $age];
        }

        return $paxes;
    }

    protected function buildBookingPaxes(BookingRequest $request): array
    {
        $paxes = [];

        // Add main guest
        $paxes[] = [
            'roomId' => 1,
            'type' => 'AD',
            'name' => $request->contactFirstName,
            'surname' => $request->contactLastName,
        ];

        // Add additional guests from request
        foreach ($request->guests as $index => $guest) {
            $paxes[] = [
                'roomId' => 1,
                'type' => $guest['type'] ?? 'AD',
                'name' => $guest['first_name'] ?? 'Guest',
                'surname' => $guest['last_name'] ?? ($index + 2),
                'age' => $guest['age'] ?? null,
            ];
        }

        return $paxes;
    }

    protected function mapBookingStatus(string $status): string
    {
        return match (strtoupper($status)) {
            'CONFIRMED' => BookingResult::STATUS_CONFIRMED,
            'CANCELLED' => BookingResult::STATUS_CANCELLED,
            'ON_REQUEST' => BookingResult::STATUS_ON_REQUEST,
            default => BookingResult::STATUS_PENDING,
        };
    }

    protected function getDestinationCode(string $destination): string
    {
        // Map common destinations to Hotelbeds codes
        // In production, this would look up from a database
        $codes = [
            'dhaka' => 'DAC',
            'chittagong' => 'CGP',
            'cox\'s bazar' => 'CXB',
            'sylhet' => 'ZYL',
        ];

        return $codes[strtolower($destination)] ?? strtoupper(substr($destination, 0, 3));
    }

    protected function buildImageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return 'https://photos.hotelbeds.com/giata/'.$path;
    }
}
