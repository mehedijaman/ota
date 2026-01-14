<?php

namespace Modules\Hotel\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Response;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Services\HotelInventoryService;
use Modules\Support\Http\Controllers\BackendController;

class HotelInventoryController extends BackendController
{
    public function __construct(protected HotelInventoryService $service) {}

    public function show(Hotel $hotel): Response
    {
        return inertia('HotelInventory/AvailabilityIndex', [
            'hotel' => $hotel,
        ]);
    }

    public function getData(Request $request, Hotel $hotel)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date|after:start',
        ]);

        // FullCalendar sends start/end query params
        $data = $this->service->getCalendarData($hotel->id, $request->start, $request->end);

        return response()->json($data);
    }

    public function update(Request $request, Hotel $hotel)
    {
        $request->validate([
            'room_id' => 'required|exists:hotel_rooms,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_inventory' => 'nullable|integer|min:0',
            'blocked' => 'nullable|integer|min:0',
        ]);

        $this->service->bulkUpdate($hotel->id, $request->all());

        return back()->with('success', 'Inventory updated successfully.');
    }
}
