<?php

namespace Modules\Hotel\Adapters\Suppliers;

use Modules\Hotel\Adapters\BaseSupplierAdapter;
use Modules\Hotel\DTOs\BookingRequest;
use Modules\Hotel\DTOs\BookingResult;
use Modules\Hotel\DTOs\HotelAvailabilityRequest;
use Modules\Hotel\DTOs\HotelAvailabilityResult;
use Modules\Hotel\DTOs\HotelSearchRequest;
use Modules\Hotel\DTOs\HotelSearchResult;

/**
 * Demo adapter for testing and development.
 *
 * Returns simulated hotel data without making real API calls.
 * Useful for testing the integration flow.
 */
class DemoAdapter extends BaseSupplierAdapter
{
    public function getSupplierCode(): string
    {
        return 'demo';
    }

    protected function getBaseUrl(): string
    {
        return 'https://demo-api.example.com/v1';
    }

    protected function getAuthHeaders(): array
    {
        return [
            'X-Api-Key' => 'demo-key',
            'Accept' => 'application/json',
        ];
    }

    public function isConfigured(): bool
    {
        return true; // Demo adapter is always configured
    }

    public function searchHotels(HotelSearchRequest $request): array
    {
        // Generate demo hotels based on destination
        $hotels = [];
        $demoHotels = $this->getDemoHotels($request->destination);

        foreach ($demoHotels as $hotel) {
            $hotels[] = new HotelSearchResult(
                supplierCode: 'demo',
                supplierHotelId: $hotel['id'],
                name: $hotel['name'],
                starRating: $hotel['stars'],
                address: $hotel['address'],
                city: $request->destination,
                country: 'Bangladesh',
                latitude: $hotel['lat'],
                longitude: $hotel['lng'],
                imageUrl: $hotel['image'],
                images: [$hotel['image']],
                amenities: $hotel['amenities'],
                minRate: $hotel['min_rate'],
                maxRate: $hotel['max_rate'],
                currency: $request->currency ?? 'BDT',
                reviewScore: $hotel['rating'],
                reviewCount: rand(50, 500),
                isAvailable: true,
            );
        }

        return $hotels;
    }

    public function getHotelDetails(string $supplierHotelId): ?array
    {
        $demoHotels = $this->getDemoHotels('Dhaka');

        foreach ($demoHotels as $hotel) {
            if ($hotel['id'] === $supplierHotelId) {
                return $hotel;
            }
        }

        return null;
    }

    public function checkAvailability(HotelAvailabilityRequest $request): HotelAvailabilityResult
    {
        $nights = $request->getNights();
        $baseRate = rand(4000, 15000);

        $rooms = [
            [
                'room_id' => 'demo-room-1',
                'room_name' => 'Deluxe Double Room',
                'room_type' => 'Deluxe',
                'max_occupancy' => 2,
                'nightly_rate' => $baseRate,
                'total_rate' => $baseRate * $nights,
                'currency' => $request->currency,
                'available_rooms' => rand(1, 5),
                'rate_key' => 'DEMO-RATE-'.uniqid(),
                'cancellation_policy' => 'Free cancellation until 24 hours before check-in',
                'meal_plan' => 'Breakfast Included',
            ],
            [
                'room_id' => 'demo-room-2',
                'room_name' => 'Premium Suite',
                'room_type' => 'Suite',
                'max_occupancy' => 4,
                'nightly_rate' => $baseRate * 1.5,
                'total_rate' => $baseRate * 1.5 * $nights,
                'currency' => $request->currency,
                'available_rooms' => rand(1, 3),
                'rate_key' => 'DEMO-RATE-'.uniqid(),
                'cancellation_policy' => 'Non-refundable',
                'meal_plan' => 'All Inclusive',
            ],
        ];

        return new HotelAvailabilityResult(
            supplierCode: 'demo',
            supplierHotelId: $request->supplierHotelId,
            isAvailable: true,
            rooms: $rooms,
            currency: $request->currency,
            cancellationPolicy: 'Free cancellation available on selected rates',
        );
    }

    public function createBooking(BookingRequest $request): BookingResult
    {
        // Simulate API delay
        usleep(500000); // 0.5 seconds

        $bookingId = 'DEMO-'.strtoupper(uniqid());
        $confirmationNumber = 'CNF-'.rand(100000, 999999);

        return new BookingResult(
            supplierCode: 'demo',
            status: BookingResult::STATUS_CONFIRMED,
            supplierBookingId: $bookingId,
            supplierConfirmationNumber: $confirmationNumber,
            message: 'Demo booking created successfully',
            totalAmount: rand(20000, 100000),
            currency: $request->currency,
            cancellationDeadline: now()->addDays(1)->toDateTimeString(),
        );
    }

    public function confirmBooking(string $supplierBookingId): BookingResult
    {
        return new BookingResult(
            supplierCode: 'demo',
            status: BookingResult::STATUS_CONFIRMED,
            supplierBookingId: $supplierBookingId,
            message: 'Demo booking confirmed',
        );
    }

    public function cancelBooking(string $supplierBookingId, string $reason): BookingResult
    {
        return new BookingResult(
            supplierCode: 'demo',
            status: BookingResult::STATUS_CANCELLED,
            supplierBookingId: $supplierBookingId,
            message: "Demo booking cancelled: {$reason}",
        );
    }

    public function getBookingStatus(string $supplierBookingId): BookingResult
    {
        return new BookingResult(
            supplierCode: 'demo',
            status: BookingResult::STATUS_CONFIRMED,
            supplierBookingId: $supplierBookingId,
            message: 'Booking is confirmed and active',
        );
    }

    public function ping(): bool
    {
        return true;
    }

    protected function getDemoHotels(string $destination): array
    {
        return [
            [
                'id' => 'demo-hotel-1',
                'name' => 'The Grand '.ucfirst($destination).' Hotel',
                'stars' => 5,
                'address' => '123 Main Street, '.$destination,
                'lat' => 23.8103 + (rand(-100, 100) / 1000),
                'lng' => 90.4125 + (rand(-100, 100) / 1000),
                'image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800',
                'amenities' => ['WiFi', 'Pool', 'Spa', 'Restaurant', 'Gym', 'Parking'],
                'min_rate' => rand(8000, 12000),
                'max_rate' => rand(15000, 25000),
                'rating' => 4.5,
            ],
            [
                'id' => 'demo-hotel-2',
                'name' => $destination.' Luxury Resort & Spa',
                'stars' => 5,
                'address' => '456 Beach Road, '.$destination,
                'lat' => 23.8103 + (rand(-100, 100) / 1000),
                'lng' => 90.4125 + (rand(-100, 100) / 1000),
                'image' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800',
                'amenities' => ['WiFi', 'Beach Access', 'Spa', 'Restaurant', 'Bar'],
                'min_rate' => rand(10000, 15000),
                'max_rate' => rand(20000, 35000),
                'rating' => 4.8,
            ],
            [
                'id' => 'demo-hotel-3',
                'name' => 'City Central Inn '.$destination,
                'stars' => 3,
                'address' => '789 City Center, '.$destination,
                'lat' => 23.8103 + (rand(-100, 100) / 1000),
                'lng' => 90.4125 + (rand(-100, 100) / 1000),
                'image' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800',
                'amenities' => ['WiFi', 'Restaurant', 'Parking'],
                'min_rate' => rand(2500, 4000),
                'max_rate' => rand(5000, 8000),
                'rating' => 4.0,
            ],
            [
                'id' => 'demo-hotel-4',
                'name' => $destination.' Business Tower',
                'stars' => 4,
                'address' => '101 Corporate Park, '.$destination,
                'lat' => 23.8103 + (rand(-100, 100) / 1000),
                'lng' => 90.4125 + (rand(-100, 100) / 1000),
                'image' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=800',
                'amenities' => ['WiFi', 'Business Center', 'Gym', 'Restaurant', 'Conference Rooms'],
                'min_rate' => rand(6000, 9000),
                'max_rate' => rand(12000, 18000),
                'rating' => 4.3,
            ],
        ];
    }
}
