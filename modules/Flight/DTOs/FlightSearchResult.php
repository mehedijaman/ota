<?php

namespace Modules\Flight\DTOs;

use Illuminate\Support\Collection;

/**
 * Data Transfer Object for flight search results.
 */
readonly class FlightSearchResult
{
    public function __construct(
        public string $supplierCode,
        public string $supplierId,
        public string $offerId,
        public string $airlineCode,
        public string $airlineName,
        public string $flightNumber,
        public string $originCode,
        public string $originName,
        public string $destinationCode,
        public string $destinationName,
        public string $departureDateTime,
        public string $arrivalDateTime,
        public int $durationMinutes,
        public int $stops,
        public array $stopoverAirports,
        public string $cabinClass,
        public string $fareClass,
        public string $fareBasis,
        public float $basePrice,
        public float $taxes,
        public float $fees,
        public float $totalPrice,
        public string $currency,
        public int $availableSeats,
        public string $baggageAllowance,
        public bool $isRefundable,
        public bool $isChangeable,
        public ?string $aircraftType = null,
        public ?string $operatingAirline = null,
        public ?string $operatingFlightNumber = null,
        public ?array $segments = [],
        public ?array $fareRules = [],
        public array $rawData = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            supplierCode: $data['supplier_code'] ?? '',
            supplierId: $data['supplier_id'] ?? '',
            offerId: $data['offer_id'] ?? '',
            airlineCode: $data['airline_code'] ?? '',
            airlineName: $data['airline_name'] ?? '',
            flightNumber: $data['flight_number'] ?? '',
            originCode: $data['origin_code'] ?? '',
            originName: $data['origin_name'] ?? '',
            destinationCode: $data['destination_code'] ?? '',
            destinationName: $data['destination_name'] ?? '',
            departureDateTime: $data['departure_datetime'] ?? '',
            arrivalDateTime: $data['arrival_datetime'] ?? '',
            durationMinutes: (int) ($data['duration_minutes'] ?? 0),
            stops: (int) ($data['stops'] ?? 0),
            stopoverAirports: $data['stopover_airports'] ?? [],
            cabinClass: $data['cabin_class'] ?? 'economy',
            fareClass: $data['fare_class'] ?? '',
            fareBasis: $data['fare_basis'] ?? '',
            basePrice: (float) ($data['base_price'] ?? 0),
            taxes: (float) ($data['taxes'] ?? 0),
            fees: (float) ($data['fees'] ?? 0),
            totalPrice: (float) ($data['total_price'] ?? 0),
            currency: $data['currency'] ?? 'BDT',
            availableSeats: (int) ($data['available_seats'] ?? 0),
            baggageAllowance: $data['baggage_allowance'] ?? '',
            isRefundable: (bool) ($data['is_refundable'] ?? false),
            isChangeable: (bool) ($data['is_changeable'] ?? true),
            aircraftType: $data['aircraft_type'] ?? null,
            operatingAirline: $data['operating_airline'] ?? null,
            operatingFlightNumber: $data['operating_flight_number'] ?? null,
            segments: $data['segments'] ?? [],
            fareRules: $data['fare_rules'] ?? [],
            rawData: $data['raw_data'] ?? $data,
        );
    }

    public function toArray(): array
    {
        return [
            'supplier_code' => $this->supplierCode,
            'supplier_id' => $this->supplierId,
            'offer_id' => $this->offerId,
            'airline_code' => $this->airlineCode,
            'airline_name' => $this->airlineName,
            'flight_number' => $this->flightNumber,
            'origin_code' => $this->originCode,
            'origin_name' => $this->originName,
            'destination_code' => $this->destinationCode,
            'destination_name' => $this->destinationName,
            'departure_datetime' => $this->departureDateTime,
            'arrival_datetime' => $this->arrivalDateTime,
            'duration_minutes' => $this->durationMinutes,
            'stops' => $this->stops,
            'stopover_airports' => $this->stopoverAirports,
            'cabin_class' => $this->cabinClass,
            'fare_class' => $this->fareClass,
            'fare_basis' => $this->fareBasis,
            'base_price' => $this->basePrice,
            'taxes' => $this->taxes,
            'fees' => $this->fees,
            'total_price' => $this->totalPrice,
            'currency' => $this->currency,
            'available_seats' => $this->availableSeats,
            'baggage_allowance' => $this->baggageAllowance,
            'is_refundable' => $this->isRefundable,
            'is_changeable' => $this->isChangeable,
            'aircraft_type' => $this->aircraftType,
            'operating_airline' => $this->operatingAirline,
            'operating_flight_number' => $this->operatingFlightNumber,
            'segments' => $this->segments,
            'fare_rules' => $this->fareRules,
        ];
    }

    public function getDurationDisplay(): string
    {
        $hours = floor($this->durationMinutes / 60);
        $minutes = $this->durationMinutes % 60;

        return "{$hours}h {$minutes}m";
    }

    public function getStopsDisplay(): string
    {
        if ($this->stops === 0) {
            return 'Direct';
        }

        return $this->stops === 1 ? '1 Stop' : "{$this->stops} Stops";
    }

    public function getPriceDisplay(): string
    {
        return number_format($this->totalPrice, 0).' '.$this->currency;
    }

    public function getRouteDisplay(): string
    {
        return "{$this->originCode} → {$this->destinationCode}";
    }

    public function getFlightDisplay(): string
    {
        return "{$this->airlineCode}{$this->flightNumber}";
    }

    public function isDirect(): bool
    {
        return $this->stops === 0;
    }

    public function isCodeshare(): bool
    {
        return $this->operatingAirline !== null && $this->operatingAirline !== $this->airlineCode;
    }

    public function hasLowAvailability(int $threshold = 5): bool
    {
        return $this->availableSeats > 0 && $this->availableSeats <= $threshold;
    }

    /**
     * Create collection from array of results.
     */
    public static function collection(array $results): Collection
    {
        return collect($results)->map(fn ($data) => self::fromArray($data));
    }
}
