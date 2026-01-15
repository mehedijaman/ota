<?php

namespace Modules\Hotel\DTOs;

/**
 * Data Transfer Object for hotel search results.
 * Provides a normalized structure for hotels from any supplier.
 */
readonly class HotelSearchResult
{
    public function __construct(
        public string $supplierCode,
        public string $supplierHotelId,
        public string $name,
        public int $starRating,
        public string $address,
        public string $city,
        public string $country,
        public ?float $latitude,
        public ?float $longitude,
        public ?string $imageUrl,
        public array $images,
        public array $amenities,
        public float $minRate,
        public float $maxRate,
        public string $currency,
        public ?float $reviewScore = null,
        public ?int $reviewCount = null,
        public bool $isAvailable = true,
        public ?int $localHotelId = null,
        public array $rooms = [],
        public array $rawData = [],
    ) {}

    public function toArray(): array
    {
        return [
            'supplier_code' => $this->supplierCode,
            'supplier_hotel_id' => $this->supplierHotelId,
            'name' => $this->name,
            'star_rating' => $this->starRating,
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'image_url' => $this->imageUrl,
            'images' => $this->images,
            'amenities' => $this->amenities,
            'min_rate' => $this->minRate,
            'max_rate' => $this->maxRate,
            'currency' => $this->currency,
            'review_score' => $this->reviewScore,
            'review_count' => $this->reviewCount,
            'is_available' => $this->isAvailable,
            'local_hotel_id' => $this->localHotelId,
            'rooms' => $this->rooms,
        ];
    }

    public function isLocal(): bool
    {
        return $this->supplierCode === 'local';
    }
}
