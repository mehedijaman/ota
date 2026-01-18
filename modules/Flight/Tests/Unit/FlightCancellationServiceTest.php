<?php

namespace Modules\Flight\Tests\Unit;

use Modules\Flight\Services\FlightCancellationService;
use Tests\TestCase;

class FlightCancellationServiceTest extends TestCase
{
    public function test_service_initialization()
    {
        $service = new FlightCancellationService();
        $this->assertInstanceOf(FlightCancellationService::class, $service);
    }
}
