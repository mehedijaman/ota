<?php

namespace Modules\Hotel\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelRoom;

class HotelRoomService
{
    public function getAllForHotel(int $hotelId): LengthAwarePaginator
    {
        return HotelRoom::query()
            ->where('hotel_id', $hotelId)
            ->with(['roomType', 'amenities'])
            ->withCount(['bookingRooms'])
            ->when(request('search'), fn ($q, $search) => $q->search($search))
            ->when(request('room_type_id'), fn ($q, $id) => $q->where('room_type_id', $id))
            ->when(request('status') === 'active', fn ($q) => $q->active())
            ->when(request('status') === 'inactive', fn ($q) => $q->where('is_active', false))
            ->orderBy(request('sort_by', 'sort_order'), request('sort_order', 'asc'))
            ->paginate(request('per_page', 15))
            ->withQueryString();
    }

    public function getById(int $id): HotelRoom
    {
        return HotelRoom::with([
            'hotel',
            'roomType',
            'amenities',
            'rates' => fn ($q) => $q->orderBy('date')->limit(30),
            'inventories' => fn ($q) => $q->orderBy('date')->limit(30),
        ])->findOrFail($id);
    }

    public function getForEdit(int $id): HotelRoom
    {
        return HotelRoom::with(['amenities'])->findOrFail($id);
    }

    public function create(array $data): HotelRoom
    {
        return DB::transaction(function () use ($data) {
            $room = HotelRoom::create(Arr::except($data, ['amenities', 'media']));

            if (! empty($data['amenities'])) {
                $this->syncAmenities($room, $data['amenities']);
            }

            if (! empty($data['media'])) {
                $this->handleMedia($room, $data['media']);
            }

            // Update hotel total rooms count
            $this->updateHotelRoomCount($room->hotel_id);

            return $room;
        });
    }

    public function update(HotelRoom $room, array $data): HotelRoom
    {
        return DB::transaction(function () use ($room, $data) {
            $room->update(Arr::except($data, ['amenities', 'media']));

            if (isset($data['amenities'])) {
                $this->syncAmenities($room, $data['amenities']);
            }

            if (! empty($data['media'])) {
                $this->handleMedia($room, $data['media']);
            }

            return $room->fresh();
        });
    }

    protected function syncAmenities(HotelRoom $room, array $amenities): void
    {
        $syncData = [];
        foreach ($amenities as $amenity) {
            $amenityId = is_array($amenity) ? $amenity['id'] : $amenity;
            $syncData[$amenityId] = [
                'quantity' => $amenity['quantity'] ?? 1,
                'notes' => $amenity['notes'] ?? null,
            ];
        }
        $room->amenities()->sync($syncData);
    }

    protected function handleMedia(HotelRoom $room, array $media): void
    {
        if (! empty($media['images'])) {
            foreach ($media['images'] as $image) {
                $room->addMedia($image)->toMediaCollection('room_images');
            }
        }
    }

    public function delete(HotelRoom $room): bool
    {
        if ($room->bookingRooms()->whereHas('booking', fn ($q) => $q->whereIn('status', ['confirmed', 'checked_in']))->exists()) {
            return false;
        }

        $hotelId = $room->hotel_id;
        $room->delete();
        $this->updateHotelRoomCount($hotelId);

        return true;
    }

    protected function updateHotelRoomCount(int $hotelId): void
    {
        $count = HotelRoom::where('hotel_id', $hotelId)->where('is_active', true)->sum('total_inventory');
        Hotel::where('id', $hotelId)->update(['total_rooms' => $count]);
    }

    public function getRecycleBin(int $hotelId): LengthAwarePaginator
    {
        return HotelRoom::onlyTrashed()
            ->where('hotel_id', $hotelId)
            ->with(['roomType'])
            ->orderByDesc('deleted_at')
            ->paginate(15)
            ->withQueryString();
    }

    public function restore(int $id): void
    {
        $room = HotelRoom::onlyTrashed()->findOrFail($id);
        $room->restore();
        $this->updateHotelRoomCount($room->hotel_id);
    }

    public function forceDelete(int $id): void
    {
        $room = HotelRoom::onlyTrashed()->findOrFail($id);
        $room->amenities()->detach();
        $room->forceDelete();
    }
}
