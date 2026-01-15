<?php

namespace Modules\Hotel\Adapters\Suppliers;

use Modules\Hotel\Adapters\Contracts\HotelSupplierAdapterInterface;
use Modules\Hotel\DTOs\BookingRequest;
use Modules\Hotel\DTOs\BookingResult;
use Modules\Hotel\DTOs\HotelAvailabilityRequest;
use Modules\Hotel\DTOs\HotelAvailabilityResult;
use Modules\Hotel\DTOs\HotelSearchRequest;
use Modules\Hotel\DTOs\HotelSearchResult;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelRoom;

/**
 * Local adapter for internal hotel inventory.
 *
 * This adapter searches and books from the local database,
 * without making any external API calls.
 */
class LocalAdapter implements HotelSupplierAdapterInterface
{
    public function getSupplierCode(): string
    {
        return 'local';
    }

    public function isConfigured(): bool
    {
        return true; // Local adapter is always available
    }

    public function searchHotels(HotelSearchRequest $request): array
    {
        $query = Hotel::query()
            ->with(['city', 'country', 'rooms.roomType'])
            ->active()
            ->where('is_active', true);

        // Filter by city/destination
        if ($request->destination) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('city', fn ($c) => $c->where('name', 'like', "%{$request->destination}%")
                    ->orWhere('slug', $request->destination))
                    ->orWhereHas('country', fn ($c) => $c->where('name', 'like', "%{$request->destination}%"))
                    ->orWhere('name', 'like', "%{$request->destination}%");
            });
        }

        // Filter by star rating
        if ($request->minStars) {
            $query->where('star_rating', '>=', $request->minStars);
        }
        if ($request->maxStars) {
            $query->where('star_rating', '<=', $request->maxStars);
        }

        // Get hotels
        $hotels = $query->limit($request->limit)->offset($request->offset)->get();

        return $hotels->map(function (Hotel $hotel) use ($request) {
            $minRate = $this->getMinRate($hotel, $request);
            $maxRate = $this->getMaxRate($hotel, $request);

            return new HotelSearchResult(
                supplierCode: 'local',
                supplierHotelId: (string) $hotel->id,
                name: $hotel->name,
                starRating: $hotel->star_rating ?? 3,
                address: $hotel->address ?? '',
                city: $hotel->city?->name ?? '',
                country: $hotel->country?->name ?? 'Bangladesh',
                latitude: $hotel->latitude,
                longitude: $hotel->longitude,
                imageUrl: $hotel->featured_image_url ?? null,
                images: $hotel->images ?? [],
                amenities: $hotel->amenities?->pluck('name')->toArray() ?? [],
                minRate: $minRate,
                maxRate: $maxRate,
                currency: $request->currency ?? 'BDT',
                reviewScore: $hotel->avg_rating,
                reviewCount: $hotel->reviews_count,
                isAvailable: true,
                localHotelId: $hotel->id,
            );
        })->toArray();
    }

    public function getHotelDetails(string $supplierHotelId): ?array
    {
        $hotel = Hotel::with(['city', 'country', 'rooms.roomType', 'amenities', 'reviews'])
            ->find($supplierHotelId);

        if (! $hotel) {
            return null;
        }

        return [
            'id' => $hotel->id,
            'name' => $hotel->name,
            'star_rating' => $hotel->star_rating,
            'address' => $hotel->address,
            'city' => $hotel->city?->name,
            'country' => $hotel->country?->name,
            'description' => $hotel->description,
            'amenities' => $hotel->amenities?->pluck('name')->toArray(),
            'rooms' => $hotel->rooms->map(fn ($r) => [
                'id' => $r->id,
                'name' => $r->name,
                'type' => $r->roomType?->name,
                'max_occupancy' => $r->max_occupancy,
                'base_price' => $r->base_price,
            ])->toArray(),
            'reviews_count' => $hotel->reviews_count,
            'avg_rating' => $hotel->avg_rating,
        ];
    }

    public function checkAvailability(HotelAvailabilityRequest $request): HotelAvailabilityResult
    {
        $hotel = Hotel::with(['rooms.roomType', 'ratePlans'])->find($request->supplierHotelId);

        if (! $hotel) {
            return new HotelAvailabilityResult(
                supplierCode: 'local',
                supplierHotelId: $request->supplierHotelId,
                isAvailable: false,
            );
        }

        $rooms = $hotel->rooms->map(function (HotelRoom $room) use ($request) {
            $nightlyRate = $room->base_price ?? 5000;
            $nights = $request->getNights();

            return [
                'room_id' => $room->id,
                'room_name' => $room->name,
                'room_type' => $room->roomType?->name,
                'max_occupancy' => $room->max_occupancy ?? 2,
                'nightly_rate' => $nightlyRate,
                'total_rate' => $nightlyRate * $nights,
                'currency' => $request->currency,
                'available_rooms' => $room->total_rooms ?? 5,
                'rate_plans' => [], // Can be populated from HotelRatePlan
            ];
        })->toArray();

        return new HotelAvailabilityResult(
            supplierCode: 'local',
            supplierHotelId: $request->supplierHotelId,
            isAvailable: count($rooms) > 0,
            rooms: $rooms,
            currency: $request->currency,
        );
    }

    public function createBooking(BookingRequest $request): BookingResult
    {
        // For local bookings, we just return a success result
        // The actual booking is handled by HotelBookingService
        return new BookingResult(
            supplierCode: 'local',
            status: BookingResult::STATUS_CONFIRMED,
            supplierBookingId: 'LOCAL-'.uniqid(),
            message: 'Booking created successfully',
            totalAmount: null, // Calculated by booking service
            currency: $request->currency,
        );
    }

    public function confirmBooking(string $supplierBookingId): BookingResult
    {
        return new BookingResult(
            supplierCode: 'local',
            status: BookingResult::STATUS_CONFIRMED,
            supplierBookingId: $supplierBookingId,
            message: 'Booking confirmed',
        );
    }

    public function cancelBooking(string $supplierBookingId, string $reason): BookingResult
    {
        return new BookingResult(
            supplierCode: 'local',
            status: BookingResult::STATUS_CANCELLED,
            supplierBookingId: $supplierBookingId,
            message: "Booking cancelled: {$reason}",
        );
    }

    public function getBookingStatus(string $supplierBookingId): BookingResult
    {
        return new BookingResult(
            supplierCode: 'local',
            status: BookingResult::STATUS_CONFIRMED,
            supplierBookingId: $supplierBookingId,
        );
    }

    public function syncHotels(?string $destinationCode = null): int
    {
        // Local hotels don't need syncing
        return 0;
    }

    public function ping(): bool
    {
        return true;
    }

    protected function getMinRate(Hotel $hotel, HotelSearchRequest $request): float
    {
        $room = $hotel->rooms->sortBy('base_price')->first();

        return $room?->base_price ?? 3000;
    }

    protected function getMaxRate(Hotel $hotel, HotelSearchRequest $request): float
    {
        $room = $hotel->rooms->sortByDesc('base_price')->first();

        return $room?->base_price ?? 10000;
    }
}
