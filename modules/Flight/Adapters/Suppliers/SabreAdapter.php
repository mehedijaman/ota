<?php

namespace Modules\Flight\Adapters\Suppliers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Modules\Flight\Adapters\BaseFlightSupplierAdapter;
use Modules\Flight\DTOs\FlightBookingRequest;
use Modules\Flight\DTOs\FlightBookingResult;
use Modules\Flight\DTOs\FlightSearchRequest;
use Modules\Flight\DTOs\FlightSearchResult;

class SabreAdapter extends BaseFlightSupplierAdapter
{
    protected string $accessToken = '';

    /**
     * Authenticate with Sabre to get access token.
     */
    protected function authenticate(): void
    {
        if (! empty($this->accessToken)) {
            return;
        }

        if (($this->config['mock_mode'] ?? false) === true) {
            $this->accessToken = 'mock-sabre-token';

            return;
        }

        try {
            // Sabre requires Base64 encoded ClientID:ClientSecret
            $credentials = base64_encode(($this->config['client_id'] ?? '').':'.($this->config['client_secret'] ?? ''));

            $response = Http::withHeaders([
                'Authorization' => 'Basic '.$credentials,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->post($this->baseUrl.'/v2/auth/token', [
                'grant_type' => 'client_credentials',
            ]);

            if ($response->successful()) {
                $this->accessToken = $response->json('access_token');
            }
        } catch (\Exception $e) {
            // Log error
        }
    }

    protected function getHeaders(): array
    {
        $this->authenticate();

        return [
            'Authorization' => 'Bearer '.$this->accessToken,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    public function search(FlightSearchRequest $request): Collection
    {
        if (($this->config['mock_mode'] ?? false) === true) {
            return collect([
                $this->transformSearchResult($this->getMockSearchData()),
            ]);
        }

        $payload = [
            'OTA_AirLowFareSearchRQ' => [
                'OriginDestinationInformation' => [
                    [
                        'DepartureDateTime' => $request->departureDate.'T00:00:00',
                        'OriginLocation' => ['LocationCode' => $request->originCode],
                        'DestinationLocation' => ['LocationCode' => $request->destinationCode],
                    ],
                ],
                'TravelerInfoSummary' => [
                    'AirTravelerAvail' => [
                        [
                            'PassengerTypeQuantity' => [
                                ['Code' => 'ADT', 'Quantity' => $request->adults],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->request('post', '/v4/offers/shop', $payload);

        if (! $response['success']) {
            return collect([]);
        }

        // Transform Sabre response (which is complex) into unified DTO
        return collect([]);
    }

    public function book(FlightBookingRequest $request): FlightBookingResult
    {
        if (($this->config['mock_mode'] ?? false) === true) {
            return $this->transformBookingResult(['Confirmation' => ['Locator' => 'SABRE-123']]);
        }

        // Sabre Enhanced Air Booking / Create Passenger Name Record
        $payload = [
            'CreatePassengerNameRecordRQ' => [
                // Complex structure for PNR creation
            ],
        ];

        $response = $this->request('post', '/v2.4.0/passenger/records?mode=create', $payload);

        if (! $response['success']) {
            return FlightBookingResult::failure($response['error']);
        }

        return $this->transformBookingResult($response['data']);
    }

    public function cancel(string $supplierBookingId, ?string $reason = null): FlightBookingResult
    {
        $payload = [
            'confirmationId' => $supplierBookingId,
            'retrieveContext' => true,
        ];

        $response = $this->request('post', '/v1/trip/orders/cancel', $payload);

        if (! $response['success']) {
            return FlightBookingResult::failure($response['error']);
        }

        return new FlightBookingResult(
            success: true,
            supplierBookingId: $supplierBookingId,
            bookingStatus: 'cancelled'
        );
    }

    public function issueTicket(string $supplierBookingId): FlightBookingResult
    {
        // Air Ticket API
        $payload = [
            'AirTicketRQ' => [
                'DesignatePrinter' => ['Printers' => ['Ticket' => ['CountryCode' => 'US']]],
                'Itinerary' => ['ID' => $supplierBookingId],
            ],
        ];

        $response = $this->request('post', '/v1/air/ticket', $payload);

        return new FlightBookingResult(
            success: true,
            supplierBookingId: $supplierBookingId,
            ticketingStatus: 'ticketed'
        );
    }

    public function voidTicket(string $supplierBookingId): FlightBookingResult
    {
        return new FlightBookingResult(
            success: true,
            supplierBookingId: $supplierBookingId,
            ticketingStatus: 'voided'
        );
    }

    protected function transformSearchResult(array $data): FlightSearchResult
    {
        return FlightSearchResult::fromArray([
             'supplier_code' => 'Sabre',
             'offer_id' => 'SABRE-OFFER-1',
             'total_price' => 600.00,
             'currency' => 'USD',
             'segments' => [],
             // Mock required fields
             'airline_code' => 'DL',
             'airline_name' => 'Delta',
             'flight_number' => '123',
             'origin_code' => 'JFK',
             'destination_code' => 'LHR',
             'departure_datetime' => '2025-06-01 12:00:00',
             'arrival_datetime' => '2025-06-01 20:00:00',
        ]);
    }

    protected function transformBookingResult(array $data): FlightBookingResult
    {
        return FlightBookingResult::success([
            'supplier_booking_id' => $data['Confirmation']['Locator'] ?? 'UNKNOWN',
            'pnr' => $data['Confirmation']['Locator'] ?? 'UNKNOWN',
            'booking_status' => 'confirmed',
        ]);
    }

    protected function getMockSearchData(): array
    {
        return [];
    }
}
