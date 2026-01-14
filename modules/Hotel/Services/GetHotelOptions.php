<?php

namespace Modules\Hotel\Services;

use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelAmenity;
use Modules\Hotel\Models\HotelChain;
use Modules\Hotel\Models\HotelRoomAmenity;
use Modules\Hotel\Models\HotelRoomType;
use Nnjeim\World\Models\City;
use Nnjeim\World\Models\Country;

class GetHotelOptions
{
    public function get(): array
    {
        return Hotel::query()
            ->select('id', 'name', 'city_id', 'star_rating')
            ->with('city:id,name')
            ->active()
            ->orderBy('name')
            ->get()
            ->map(fn ($hotel) => [
                'id' => $hotel->id,
                'name' => $hotel->name,
                'city' => $hotel->city?->name,
                'star_rating' => $hotel->star_rating,
                'label' => "{$hotel->name} ({$hotel->star_rating}★)",
            ])
            ->toArray();
    }

    public function getCountries(): array
    {
        return Country::query()
            ->select('id', 'name', 'iso2', 'iso3', 'phone_code')
            ->orderBy('name')
            ->get()
            ->map(fn ($country) => [
                'id' => $country->id,
                'name' => $country->name,
                'code' => $country->iso2,
                'phone_code' => $country->phone_code,
            ])
            ->toArray();
    }

    public function getCities(?int $countryId = null): array
    {
        return City::query()
            ->select('id', 'name', 'country_id')
            ->when($countryId, fn ($q) => $q->where('country_id', $countryId))
            ->orderBy('name')
            ->limit(500)
            ->get()
            ->map(fn ($city) => [
                'id' => $city->id,
                'name' => $city->name,
                'country_id' => $city->country_id,
            ])
            ->toArray();
    }

    public function getChains(): array
    {
        return HotelChain::query()
            ->select('id', 'name', 'code')
            ->active()
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function getRoomTypes(): array
    {
        return HotelRoomType::query()
            ->select('id', 'name', 'code')
            ->active()
            ->ordered()
            ->get()
            ->toArray();
    }

    public function getHotelAmenities(): array
    {
        return HotelAmenity::query()
            ->select('id', 'name', 'slug', 'icon', 'category', 'is_popular')
            ->active()
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category')
            ->toArray();
    }

    public function getRoomAmenities(): array
    {
        return HotelRoomAmenity::query()
            ->select('id', 'name', 'slug', 'icon', 'category')
            ->active()
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category')
            ->toArray();
    }

    public function getPropertyTypes(): array
    {
        return [
            ['value' => 'hotel', 'label' => 'Hotel'],
            ['value' => 'resort', 'label' => 'Resort'],
            ['value' => 'apartment', 'label' => 'Apartment'],
            ['value' => 'villa', 'label' => 'Villa'],
            ['value' => 'boutique', 'label' => 'Boutique Hotel'],
            ['value' => 'hostel', 'label' => 'Hostel'],
            ['value' => 'guesthouse', 'label' => 'Guesthouse'],
            ['value' => 'motel', 'label' => 'Motel'],
            ['value' => 'lodge', 'label' => 'Lodge'],
            ['value' => 'inn', 'label' => 'Inn'],
        ];
    }

    public function getStarRatings(): array
    {
        return [
            ['value' => 1, 'label' => '1 Star'],
            ['value' => 2, 'label' => '2 Stars'],
            ['value' => 3, 'label' => '3 Stars'],
            ['value' => 4, 'label' => '4 Stars'],
            ['value' => 5, 'label' => '5 Stars'],
        ];
    }

    public function getBedTypes(): array
    {
        return [
            ['value' => 'single', 'label' => 'Single Bed'],
            ['value' => 'double', 'label' => 'Double Bed'],
            ['value' => 'queen', 'label' => 'Queen Bed'],
            ['value' => 'king', 'label' => 'King Bed'],
            ['value' => 'twin', 'label' => 'Twin Beds'],
            ['value' => 'bunk', 'label' => 'Bunk Beds'],
            ['value' => 'sofa_bed', 'label' => 'Sofa Bed'],
        ];
    }
}
