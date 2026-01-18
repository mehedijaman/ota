<?php

namespace Modules\Flight\Tests\Unit;

use Modules\Flight\DTOs\FlightBookingRequest;
use Modules\Flight\DTOs\FlightSearchRequest;
use Modules\Flight\DTOs\FlightSearchResult;
use Tests\TestCase;

class FlightDtoTest extends TestCase
{
    public function test_flight_search_request_initialization()
    {
        $data = [
            'origin_code' => 'DAC',
            'destination_code' => 'CXB',
            'departure_date' => '2025-01-01',
            'adults' => 2,
            'cabin_class' => 'business',
        ];

        $request = FlightSearchRequest::fromArray($data);

        $this->assertEquals('DAC', $request->originCode);
        $this->assertEquals('CXB', $request->destinationCode);
        $this->assertEquals(2, $request->adults);
        $this->assertEquals('business', $request->cabinClass);
        $this->assertEquals('one_way', $request->tripType); // Default
    }

    public function test_flight_booking_request_initialization()
    {
        $data = [
            'supplier_code' => 'amadeus',
            'offer_id' => 'offer-123',
            'trip_type' => 'round_trip',
            'departure_date' => '2025-05-01',
            'return_date' => '2025-05-10',
            'adults' => 1,
            'contact_email' => 'test@example.com',
            'passengers' => [
                ['passenger_type' => 'adult', 'first_name' => 'John', 'last_name' => 'Doe']
            ]
        ];

        $bookingRequest = FlightBookingRequest::fromArray($data);

        $this->assertEquals('amadeus', $bookingRequest->supplierCode);
        $this->assertEquals('offer-123', $bookingRequest->offerId);
        $this->assertEquals('round_trip', $bookingRequest->tripType);
        $this->assertEquals('test@example.com', $bookingRequest->contactEmail);
        $this->assertCount(1, $bookingRequest->passengers);
    }

    public function test_flight_search_result_initialization()
    {
        $data = [
            'supplier_code' => 'sabre',
            'offer_id' => 'sabre-1',
            'base_price' => 100,
            'taxes' => 50,
            'total_price' => 150,
            'airline_code' => 'BG',
            'airline_name' => 'Biman',
            'origin_code' => 'DAC',
            'destination_code' => 'LHR',
        ];

        $result = FlightSearchResult::fromArray($data);

        $this->assertEquals('SABRE', strtoupper($result->supplierCode));
        $this->assertEquals(150, $result->totalPrice);
        $this->assertEquals('150 BDT', $result->getPriceDisplay()); // Default currency is BDT in DTO
        // Actually, currency default is BDT in DTO, but check input
    }
}
