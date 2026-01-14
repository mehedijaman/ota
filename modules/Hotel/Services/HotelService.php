<?php

namespace Modules\Hotel\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\Hotel\Models\Hotel;

class HotelService
{
    public function getAll(): LengthAwarePaginator
    {
        return Hotel::query()
            ->with(['city', 'country', 'chain'])
            ->withCount(['rooms', 'bookings', 'reviews'])
            ->when(request('search'), fn ($q, $search) => $q->search($search))
            ->when(request('city_id'), fn ($q, $id) => $q->where('city_id', $id))
            ->when(request('country_id'), fn ($q, $id) => $q->where('country_id', $id))
            ->when(request('star_rating'), fn ($q, $rating) => $q->where('star_rating', $rating))
            ->when(request('property_type'), fn ($q, $type) => $q->where('property_type', $type))
            ->when(request('status') === 'active', fn ($q) => $q->active())
            ->when(request('status') === 'inactive', fn ($q) => $q->where('is_active', false))
            ->when(request('status') === 'featured', fn ($q) => $q->featured())
            ->orderBy(request('sort_by', 'created_at'), request('sort_order', 'desc'))
            ->paginate(request('per_page', 15))
            ->withQueryString();
    }

    public function getStats(): array
    {
        return [
            'total' => Hotel::count(),
            'active' => Hotel::active()->count(),
            'featured' => Hotel::featured()->count(),
            'total_rooms' => Hotel::sum('total_rooms'),
            'avg_rating' => round(Hotel::whereNotNull('avg_rating')->avg('avg_rating'), 2),
            'pending_reviews' => \Modules\Hotel\Models\HotelReview::where('moderation_status', 'pending')->count(),
        ];
    }

    public function getById(int $id): Hotel
    {
        return Hotel::with([
            'city',
            'country',
            'chain',
            'rooms' => fn ($q) => $q->with('roomType')->active(),
            'amenities' => fn ($q) => $q->orderBy('sort_order'),
            'ratePlans' => fn ($q) => $q->active(),
            'reviews' => fn ($q) => $q->published()->latest()->limit(5),
            'faqs' => fn ($q) => $q->active()->ordered(),
        ])
            ->withCount(['rooms', 'bookings', 'reviews'])
            ->findOrFail($id);
    }

    public function getForEdit(int $id): Hotel
    {
        return Hotel::with([
            'amenities',
            'rooms.roomType',
            'rooms.amenities',
            'ratePlans',
            'cancellationPolicies',
        ])->findOrFail($id);
    }

    public function create(array $data): Hotel
    {
        return DB::transaction(function () use ($data) {
            $hotel = Hotel::create(Arr::except($data, ['amenities', 'media']));

            if (! empty($data['amenities'])) {
                $this->syncAmenities($hotel, $data['amenities']);
            }

            if (! empty($data['media'])) {
                $this->handleMedia($hotel, $data['media']);
            }

            return $hotel;
        });
    }

    public function update(Hotel $hotel, array $data): Hotel
    {
        return DB::transaction(function () use ($hotel, $data) {
            $hotel->update(Arr::except($data, ['amenities', 'media']));

            if (isset($data['amenities'])) {
                $this->syncAmenities($hotel, $data['amenities']);
            }

            if (! empty($data['media'])) {
                $this->handleMedia($hotel, $data['media']);
            }

            return $hotel->fresh();
        });
    }

    protected function syncAmenities(Hotel $hotel, array $amenities): void
    {
        $syncData = [];
        foreach ($amenities as $amenity) {
            $amenityId = is_array($amenity) ? $amenity['id'] : $amenity;
            $syncData[$amenityId] = [
                'is_free' => $amenity['is_free'] ?? true,
                'fee' => $amenity['fee'] ?? null,
                'fee_type' => $amenity['fee_type'] ?? null,
                'notes' => $amenity['notes'] ?? null,
            ];
        }
        $hotel->amenities()->sync($syncData);
    }

    protected function handleMedia(Hotel $hotel, array $media): void
    {
        if (! empty($media['thumbnail'])) {
            $hotel->addMedia($media['thumbnail'])->toMediaCollection('thumbnail');
        }

        if (! empty($media['gallery'])) {
            foreach ($media['gallery'] as $image) {
                $hotel->addMedia($image)->toMediaCollection('gallery');
            }
        }
    }

    public function delete(Hotel $hotel): bool
    {
        if ($hotel->bookings()->whereIn('status', ['confirmed', 'checked_in'])->exists()) {
            return false;
        }

        $hotel->delete();

        return true;
    }

    public function getRecycleBin(): LengthAwarePaginator
    {
        return Hotel::onlyTrashed()
            ->with(['city', 'country'])
            ->orderByDesc('deleted_at')
            ->paginate(15)
            ->withQueryString();
    }

    public function restore(int $id): void
    {
        Hotel::onlyTrashed()->findOrFail($id)->restore();
    }

    public function forceDelete(int $id): void
    {
        $hotel = Hotel::onlyTrashed()->findOrFail($id);
        $hotel->amenities()->detach();
        $hotel->forceDelete();
    }

    public function emptyRecycleBin(): void
    {
        Hotel::onlyTrashed()->each(function ($hotel) {
            $hotel->amenities()->detach();
            $hotel->forceDelete();
        });
    }

    public function restoreAll(): void
    {
        Hotel::onlyTrashed()->restore();
    }
}
