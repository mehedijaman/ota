<?php

namespace Modules\Hotel\DTOs;

/**
 * Data Transfer Object for availability check results.
 */
readonly class HotelAvailabilityResult
{
    public function __construct(
        public string $supplierCode,
        public string $supplierHotelId,
        public bool $isAvailable,
        public array $rooms = [],
        public ?string $currency = 'BDT',
        public ?string $cancellationPolicy = null,
        public ?string $boardBasis = null,
        public array $rawData = [],
    ) {}

    public function toArray(): array
    {
        return [
            'supplier_code' => $this->supplierCode,
            'supplier_hotel_id' => $this->supplierHotelId,
            'is_available' => $this->isAvailable,
            'rooms' => $this->rooms,
            'currency' => $this->currency,
            'cancellation_policy' => $this->cancellationPolicy,
            'board_basis' => $this->boardBasis,
        ];
    }
}
