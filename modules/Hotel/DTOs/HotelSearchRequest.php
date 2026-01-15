<?php

namespace Modules\Hotel\DTOs;

/**
 * Data Transfer Object for hotel search requests.
 */
readonly class HotelSearchRequest
{
    public function __construct(
        public string $destination,
        public string $checkIn,
        public string $checkOut,
        public int $rooms = 1,
        public int $adults = 2,
        public int $children = 0,
        public array $childAges = [],
        public ?string $nationality = null,
        public ?int $minStars = null,
        public ?int $maxStars = null,
        public ?float $minPrice = null,
        public ?float $maxPrice = null,
        public ?string $sortBy = null,
        public ?string $currency = 'BDT',
        public int $limit = 50,
        public int $offset = 0,
    ) {}

    public function toArray(): array
    {
        return [
            'destination' => $this->destination,
            'check_in' => $this->checkIn,
            'check_out' => $this->checkOut,
            'rooms' => $this->rooms,
            'adults' => $this->adults,
            'children' => $this->children,
            'child_ages' => $this->childAges,
            'nationality' => $this->nationality,
            'min_stars' => $this->minStars,
            'max_stars' => $this->maxStars,
            'min_price' => $this->minPrice,
            'max_price' => $this->maxPrice,
            'sort_by' => $this->sortBy,
            'currency' => $this->currency,
            'limit' => $this->limit,
            'offset' => $this->offset,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            destination: $data['destination'] ?? $data['city'] ?? '',
            checkIn: $data['check_in'] ?? $data['checkIn'] ?? '',
            checkOut: $data['check_out'] ?? $data['checkOut'] ?? '',
            rooms: (int) ($data['rooms'] ?? 1),
            adults: (int) ($data['adults'] ?? 2),
            children: (int) ($data['children'] ?? 0),
            childAges: $data['child_ages'] ?? [],
            nationality: $data['nationality'] ?? null,
            minStars: isset($data['min_stars']) ? (int) $data['min_stars'] : null,
            maxStars: isset($data['max_stars']) ? (int) $data['max_stars'] : null,
            minPrice: isset($data['min_price']) ? (float) $data['min_price'] : null,
            maxPrice: isset($data['max_price']) ? (float) $data['max_price'] : null,
            sortBy: $data['sort_by'] ?? null,
            currency: $data['currency'] ?? 'BDT',
            limit: (int) ($data['limit'] ?? 50),
            offset: (int) ($data['offset'] ?? 0),
        );
    }

    public function getNights(): int
    {
        return max(1, (int) \Carbon\Carbon::parse($this->checkIn)->diffInDays($this->checkOut));
    }
}
