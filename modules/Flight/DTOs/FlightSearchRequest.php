<?php

namespace Modules\Flight\DTOs;

/**
 * Data Transfer Object for flight search requests.
 */
readonly class FlightSearchRequest
{
    public function __construct(
        public string $tripType,
        public string $originCode,
        public string $destinationCode,
        public string $departureDate,
        public ?string $returnDate,
        public int $adults,
        public int $children = 0,
        public int $infants = 0,
        public array $childAges = [],
        public string $cabinClass = 'economy',
        public bool $directOnly = false,
        public bool $flexibleDates = false,
        public ?string $preferredAirline = null,
        public ?string $currency = 'BDT',
        public ?int $maxStops = null,
        public ?string $departureTimeRange = null,
        public ?string $arrivalTimeRange = null,
        public ?int $customerId = null,
        public ?int $agentId = null,
        public ?string $source = 'website',
        public array $supplierCodes = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            tripType: $data['trip_type'] ?? 'one_way',
            originCode: strtoupper($data['origin'] ?? $data['origin_code'] ?? ''),
            destinationCode: strtoupper($data['destination'] ?? $data['destination_code'] ?? ''),
            departureDate: $data['departure_date'] ?? '',
            returnDate: $data['return_date'] ?? null,
            adults: (int) ($data['adults'] ?? 1),
            children: (int) ($data['children'] ?? 0),
            infants: (int) ($data['infants'] ?? 0),
            childAges: $data['child_ages'] ?? [],
            cabinClass: $data['cabin_class'] ?? 'economy',
            directOnly: (bool) ($data['direct_only'] ?? false),
            flexibleDates: (bool) ($data['flexible_dates'] ?? false),
            preferredAirline: $data['preferred_airline'] ?? null,
            currency: $data['currency'] ?? 'BDT',
            maxStops: isset($data['max_stops']) ? (int) $data['max_stops'] : null,
            departureTimeRange: $data['departure_time_range'] ?? null,
            arrivalTimeRange: $data['arrival_time_range'] ?? null,
            customerId: isset($data['customer_id']) ? (int) $data['customer_id'] : null,
            agentId: isset($data['agent_id']) ? (int) $data['agent_id'] : null,
            source: $data['source'] ?? 'website',
            supplierCodes: $data['supplier_codes'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'trip_type' => $this->tripType,
            'origin_code' => $this->originCode,
            'destination_code' => $this->destinationCode,
            'departure_date' => $this->departureDate,
            'return_date' => $this->returnDate,
            'adults' => $this->adults,
            'children' => $this->children,
            'infants' => $this->infants,
            'child_ages' => $this->childAges,
            'cabin_class' => $this->cabinClass,
            'direct_only' => $this->directOnly,
            'flexible_dates' => $this->flexibleDates,
            'preferred_airline' => $this->preferredAirline,
            'currency' => $this->currency,
            'max_stops' => $this->maxStops,
            'departure_time_range' => $this->departureTimeRange,
            'arrival_time_range' => $this->arrivalTimeRange,
            'customer_id' => $this->customerId,
            'agent_id' => $this->agentId,
            'source' => $this->source,
            'supplier_codes' => $this->supplierCodes,
        ];
    }

    public function isRoundTrip(): bool
    {
        return $this->tripType === 'round_trip';
    }

    public function isOneWay(): bool
    {
        return $this->tripType === 'one_way';
    }

    public function isMultiCity(): bool
    {
        return $this->tripType === 'multi_city';
    }

    public function getTotalPassengers(): int
    {
        return $this->adults + $this->children + $this->infants;
    }

    public function getPassengerSummary(): string
    {
        $parts = [];
        if ($this->adults > 0) {
            $parts[] = "{$this->adults} Adult".($this->adults > 1 ? 's' : '');
        }
        if ($this->children > 0) {
            $parts[] = "{$this->children} Child".($this->children > 1 ? 'ren' : '');
        }
        if ($this->infants > 0) {
            $parts[] = "{$this->infants} Infant".($this->infants > 1 ? 's' : '');
        }

        return implode(', ', $parts);
    }

    public function getRouteDisplay(): string
    {
        if ($this->isRoundTrip()) {
            return "{$this->originCode} ⇄ {$this->destinationCode}";
        }

        return "{$this->originCode} → {$this->destinationCode}";
    }

    public function getCacheKey(): string
    {
        return md5(json_encode([
            $this->tripType,
            $this->originCode,
            $this->destinationCode,
            $this->departureDate,
            $this->returnDate,
            $this->adults,
            $this->children,
            $this->infants,
            $this->cabinClass,
            $this->directOnly,
        ]));
    }
}
