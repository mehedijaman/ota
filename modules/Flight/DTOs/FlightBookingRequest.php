<?php

namespace Modules\Flight\DTOs;

/**
 * Data Transfer Object for flight booking requests to suppliers.
 */
readonly class FlightBookingRequest
{
    public function __construct(
        public string $supplierCode,
        public string $offerId,
        public string $tripType,
        public string $departureDate,
        public ?string $returnDate,
        public int $adults,
        public int $children,
        public int $infants,
        public string $contactTitle,
        public string $contactFirstName,
        public string $contactLastName,
        public string $contactEmail,
        public string $contactPhone,
        public ?string $contactPhoneCountryCode = '+880',
        public ?string $contactAddress = null,
        public ?string $contactCity = null,
        public ?int $contactCountryId = null,
        public ?string $specialRequests = null,
        public array $passengers = [],
        public array $segments = [],
        public ?string $currency = 'BDT',
        public ?int $localBookingId = null,
        public ?int $customerId = null,
        public ?int $agentId = null,
        public ?string $bookingSource = 'website',
        public array $rawData = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            supplierCode: $data['supplier_code'] ?? '',
            offerId: $data['offer_id'] ?? '',
            tripType: $data['trip_type'] ?? 'one_way',
            departureDate: $data['departure_date'] ?? '',
            returnDate: $data['return_date'] ?? null,
            adults: (int) ($data['adults'] ?? $data['adult_count'] ?? 1),
            children: (int) ($data['children'] ?? $data['child_count'] ?? 0),
            infants: (int) ($data['infants'] ?? $data['infant_count'] ?? 0),
            contactTitle: $data['contact_title'] ?? 'Mr',
            contactFirstName: $data['contact_first_name'] ?? '',
            contactLastName: $data['contact_last_name'] ?? '',
            contactEmail: $data['contact_email'] ?? '',
            contactPhone: $data['contact_phone'] ?? '',
            contactPhoneCountryCode: $data['contact_phone_country_code'] ?? '+880',
            contactAddress: $data['contact_address'] ?? null,
            contactCity: $data['contact_city'] ?? null,
            contactCountryId: isset($data['contact_country_id']) ? (int) $data['contact_country_id'] : null,
            specialRequests: $data['special_requests'] ?? null,
            passengers: $data['passengers'] ?? [],
            segments: $data['segments'] ?? [],
            currency: $data['currency'] ?? 'BDT',
            localBookingId: isset($data['local_booking_id']) ? (int) $data['local_booking_id'] : null,
            customerId: isset($data['customer_id']) ? (int) $data['customer_id'] : null,
            agentId: isset($data['agent_id']) ? (int) $data['agent_id'] : null,
            bookingSource: $data['booking_source'] ?? 'website',
            rawData: $data,
        );
    }

    public function toArray(): array
    {
        return [
            'supplier_code' => $this->supplierCode,
            'offer_id' => $this->offerId,
            'trip_type' => $this->tripType,
            'departure_date' => $this->departureDate,
            'return_date' => $this->returnDate,
            'adults' => $this->adults,
            'children' => $this->children,
            'infants' => $this->infants,
            'contact_title' => $this->contactTitle,
            'contact_first_name' => $this->contactFirstName,
            'contact_last_name' => $this->contactLastName,
            'contact_email' => $this->contactEmail,
            'contact_phone' => $this->contactPhone,
            'contact_phone_country_code' => $this->contactPhoneCountryCode,
            'contact_address' => $this->contactAddress,
            'contact_city' => $this->contactCity,
            'contact_country_id' => $this->contactCountryId,
            'special_requests' => $this->specialRequests,
            'passengers' => $this->passengers,
            'segments' => $this->segments,
            'currency' => $this->currency,
            'local_booking_id' => $this->localBookingId,
            'customer_id' => $this->customerId,
            'agent_id' => $this->agentId,
            'booking_source' => $this->bookingSource,
        ];
    }

    public function getContactFullName(): string
    {
        return trim("{$this->contactTitle} {$this->contactFirstName} {$this->contactLastName}");
    }

    public function getContactPhoneFull(): string
    {
        return "{$this->contactPhoneCountryCode}{$this->contactPhone}";
    }

    public function getTotalPassengers(): int
    {
        return $this->adults + $this->children + $this->infants;
    }

    public function getAdultPassengers(): array
    {
        return array_filter($this->passengers, fn ($p) => ($p['passenger_type'] ?? 'adult') === 'adult');
    }

    public function getChildPassengers(): array
    {
        return array_filter($this->passengers, fn ($p) => ($p['passenger_type'] ?? '') === 'child');
    }

    public function getInfantPassengers(): array
    {
        return array_filter($this->passengers, fn ($p) => ($p['passenger_type'] ?? '') === 'infant');
    }

    public function getLeadPassenger(): ?array
    {
        foreach ($this->passengers as $passenger) {
            if ($passenger['is_lead_passenger'] ?? false) {
                return $passenger;
            }
        }

        return $this->passengers[0] ?? null;
    }
}
