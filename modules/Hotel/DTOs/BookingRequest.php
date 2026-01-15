<?php

namespace Modules\Hotel\DTOs;

/**
 * Data Transfer Object for booking requests to suppliers.
 */
readonly class BookingRequest
{
    public function __construct(
        public string $supplierCode,
        public string $supplierHotelId,
        public string $supplierRoomId,
        public string $supplierRateKey,
        public string $checkIn,
        public string $checkOut,
        public int $rooms,
        public int $adults,
        public int $children,
        public array $childAges,
        public string $contactTitle,
        public string $contactFirstName,
        public string $contactLastName,
        public string $contactEmail,
        public string $contactPhone,
        public ?string $contactAddress = null,
        public ?string $contactCity = null,
        public ?string $contactCountry = null,
        public ?string $specialRequests = null,
        public array $guests = [],
        public ?string $currency = 'BDT',
        public ?int $localBookingId = null,
        public array $rawData = [],
    ) {}

    public function toArray(): array
    {
        return [
            'supplier_code' => $this->supplierCode,
            'supplier_hotel_id' => $this->supplierHotelId,
            'supplier_room_id' => $this->supplierRoomId,
            'supplier_rate_key' => $this->supplierRateKey,
            'check_in' => $this->checkIn,
            'check_out' => $this->checkOut,
            'rooms' => $this->rooms,
            'adults' => $this->adults,
            'children' => $this->children,
            'child_ages' => $this->childAges,
            'contact_title' => $this->contactTitle,
            'contact_first_name' => $this->contactFirstName,
            'contact_last_name' => $this->contactLastName,
            'contact_email' => $this->contactEmail,
            'contact_phone' => $this->contactPhone,
            'contact_address' => $this->contactAddress,
            'contact_city' => $this->contactCity,
            'contact_country' => $this->contactCountry,
            'special_requests' => $this->specialRequests,
            'guests' => $this->guests,
            'currency' => $this->currency,
            'local_booking_id' => $this->localBookingId,
        ];
    }

    public function getContactFullName(): string
    {
        return trim("{$this->contactTitle} {$this->contactFirstName} {$this->contactLastName}");
    }
}
