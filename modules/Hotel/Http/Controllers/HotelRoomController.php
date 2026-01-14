<?php

namespace Modules\Hotel\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Hotel\Http\Requests\HotelRoomValidate;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelRoom;
use Modules\Hotel\Services\GetHotelOptions;
use Modules\Hotel\Services\HotelRoomService;
use Modules\Support\Http\Controllers\BackendController;

class HotelRoomController extends BackendController
{
    public function __construct(
        protected HotelRoomService $roomService,
        protected GetHotelOptions $hotelOptions
    ) {}

    public function index(int $hotelId): Response
    {
        $hotel = Hotel::findOrFail($hotelId);

        return inertia('HotelRoom/RoomIndex', [
            'hotel' => $hotel,
            'rooms' => $this->roomService->getAllForHotel($hotelId),
            'roomTypes' => $this->hotelOptions->getRoomTypes(),
        ]);
    }

    public function create(int $hotelId): Response
    {
        $hotel = Hotel::findOrFail($hotelId);

        return inertia('HotelRoom/RoomForm', [
            'hotel' => $hotel,
            'roomTypes' => $this->hotelOptions->getRoomTypes(),
            'bedTypes' => $this->hotelOptions->getBedTypes(),
            'amenities' => $this->hotelOptions->getRoomAmenities(),
        ]);
    }

    public function store(HotelRoomValidate $request, int $hotelId): RedirectResponse
    {
        $data = $request->validated();
        $data['hotel_id'] = $hotelId;

        $room = $this->roomService->create($data);

        return redirect()->route('hotel.room.index', $hotelId)
            ->with('success', 'Room created successfully.');
    }

    public function show(int $hotelId, int $roomId): Response
    {
        return inertia('HotelRoom/RoomShow', [
            'hotel' => Hotel::findOrFail($hotelId),
            'room' => $this->roomService->getById($roomId),
        ]);
    }

    public function edit(int $hotelId, int $roomId): Response
    {
        return inertia('HotelRoom/RoomForm', [
            'hotel' => Hotel::findOrFail($hotelId),
            'room' => $this->roomService->getForEdit($roomId),
            'roomTypes' => $this->hotelOptions->getRoomTypes(),
            'bedTypes' => $this->hotelOptions->getBedTypes(),
            'amenities' => $this->hotelOptions->getRoomAmenities(),
        ]);
    }

    public function update(HotelRoomValidate $request, int $hotelId, int $roomId): RedirectResponse
    {
        $room = HotelRoom::where('hotel_id', $hotelId)->findOrFail($roomId);

        $this->roomService->update($room, $request->validated());

        return redirect()->route('hotel.room.index', $hotelId)
            ->with('success', 'Room updated successfully.');
    }

    public function destroy(int $hotelId, int $roomId): RedirectResponse
    {
        $room = HotelRoom::where('hotel_id', $hotelId)->findOrFail($roomId);

        if (! $this->roomService->delete($room)) {
            return redirect()->route('hotel.room.index', $hotelId)
                ->with('error', 'Cannot delete room with active bookings.');
        }

        return redirect()->route('hotel.room.index', $hotelId)
            ->with('success', 'Room deleted successfully.');
    }

    public function recycleBin(int $hotelId): Response
    {
        return inertia('HotelRoom/RoomRecycleBin', [
            'hotel' => Hotel::findOrFail($hotelId),
            'rooms' => $this->roomService->getRecycleBin($hotelId),
        ]);
    }

    public function restore(int $hotelId, int $roomId): RedirectResponse
    {
        $this->roomService->restore($roomId);

        return redirect()->route('hotel.room.recycleBin', $hotelId)
            ->with('success', 'Room restored successfully.');
    }

    public function destroyForce(int $hotelId, int $roomId): RedirectResponse
    {
        $this->roomService->forceDelete($roomId);

        return redirect()->route('hotel.room.recycleBin', $hotelId)
            ->with('success', 'Room permanently deleted.');
    }
}
