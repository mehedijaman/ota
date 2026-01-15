<?php

namespace Modules\Hotel\DTOs;

/**
 * Data Transfer Object for availability check requests.
 */
readonly class HotelAvailabilityRequest
{
    public function __construct(
        public string $supplierCode,
        public string $supplierHotelId,
        public string $checkIn,
        public string $checkOut,
        public int $rooms = 1,
        public int $adults = 2,
        public int $children = 0,
        public array $childAges = [],
        public ?string $nationality = null,
        public ?string $currency = 'BDT',
        public ?int $localHotelId = null,
    ) {}

    public function toArray(): array
    {
        return [
            'supplier_code' => $this->supplierCode,
            'supplier_hotel_id' => $this->supplierHotelId,
            'check_in' => $this->checkIn,
            'check_out' => $this->checkOut,
            'rooms' => $this->rooms,
            'adults' => $this->adults,
            'children' => $this->children,
            'child_ages' => $this->childAges,
            'nationality' => $this->nationality,
            'currency' => $this->currency,
            'local_hotel_id' => $this->localHotelId,
        ];
    }

    public function getNights(): int
    {
        return max(1, (int) \Carbon\Carbon::parse($this->checkIn)->diffInDays($this->checkOut));
    }
}
