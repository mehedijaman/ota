<?php

namespace Modules\Flight\Tests\Unit;

use Modules\Flight\Adapters\Suppliers\AmadeusAdapter;
use Modules\Flight\Adapters\Suppliers\SabreAdapter;
use Modules\Flight\DTOs\FlightBookingRequest;
use Modules\Flight\DTOs\FlightSearchRequest;
use Modules\Flight\Models\FlightSupplier;
use Tests\TestCase;

class FlightAdapterTest extends TestCase
{
    public function test_amadeus_adapter_mock_search()
    {
        $supplier = new FlightSupplier([
            'code' => 'amadeus',
            'name' => 'Amadeus',
            'is_active' => true,
            'configuration' => ['mock_mode' => true]
        ]);

        $adapter = new AmadeusAdapter($supplier);
        $request = FlightSearchRequest::fromArray([
            'origin' => 'DAC',
            'destination' => 'CXB',
            'departure_date' => '2025-01-01',
            'adults' => 1
        ]);

        $results = $adapter->search($request);

        $this->assertNotEmpty($results);
        $this->assertEquals('Amadeus', $results->first()->supplierCode);
    }

    public function test_sabre_adapter_mock_search()
    {
        $supplier = new FlightSupplier([
            'code' => 'sabre',
            'name' => 'Sabre',
            'is_active' => true,
            'configuration' => ['mock_mode' => true]
        ]);

        $adapter = new SabreAdapter($supplier);
        $request = FlightSearchRequest::fromArray([
            'origin' => 'DAC',
            'destination' => 'CXB',
            'departure_date' => '2025-01-01',
            'adults' => 1
        ]);

        $results = $adapter->search($request);

        $this->assertNotEmpty($results);
        $this->assertEquals('Sabre', $results->first()->supplierCode);
    }

    public function test_amadeus_adapter_mock_booking()
    {
        $supplier = new FlightSupplier([
            'code' => 'amadeus',
            'configuration' => ['mock_mode' => true]
        ]);

        $adapter = new AmadeusAdapter($supplier);
        $request = FlightBookingRequest::fromArray([
             'supplier_code' => 'amadeus',
             'offer_id' => '123',
             'departure_date' => '2025-01-01',
             'adults' => 1,
             'contact_email' => 'test@test.com'
        ]);

        $result = $adapter->book($request);

        $this->assertTrue($result->success);
        $this->assertNotEmpty($result->supplierBookingId);
    }
}
