<?php

namespace Modules\Hotel\Services;

use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Modules\Hotel\Models\HotelInventory;
use Modules\Hotel\Models\HotelRoom;

class HotelInventoryService
{
    public function getCalendarData(int $hotelId, string $startDate, string $endDate): array
    {
        $rooms = HotelRoom::where('hotel_id', $hotelId)->with('roomType')->active()->ordered()->get();
        $roomIds = $rooms->pluck('id')->toArray();

        // Fetch existing inventories
        $inventories = HotelInventory::whereIn('room_id', $roomIds)
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy('room_id');

        $result = [];
        $period = CarbonPeriod::create($startDate, $endDate);

        foreach ($rooms as $room) {
            $roomInventories = $inventories->get($room->id, collect());
            $dailyData = [];

            // Map existing data, fill gaps with default derived from room
            foreach ($period as $date) {
                $dateStr = $date->format('Y-m-d');
                $inv = $roomInventories->firstWhere('date', $dateStr); // Since 'date' cast is datetime/date
                // Actually where in collection uses strict check if object?
                // $inv->date is Carbon usually due to cast.

                // Use a key-based lookup for speed if needed, but this is fine.
                $found = $roomInventories->filter(fn ($i) => $i->date->format('Y-m-d') === $dateStr)->first();

                if ($found) {
                    $dailyData[$dateStr] = [
                        'date' => $dateStr,
                        'total_inventory' => $found->total_inventory,
                        'available' => $found->available,
                        'booked' => $found->booked,
                        'blocked' => $found->blocked,
                        'is_persisted' => true,
                    ];
                } else {
                    // Not persisted -> implicitly available = total_inventory (or 0 if we assume whitelist?)
                    // Codebase seems to assume whitelist (empty = 0 availability).
                    // So we show 0 available if not initialized.
                    $dailyData[$dateStr] = [
                        'date' => $dateStr,
                        'total_inventory' => $room->total_inventory,
                        'available' => 0, // Uninitialized means NOT available for booking in search service
                        'booked' => 0,
                        'blocked' => 0,
                        'is_persisted' => false,
                    ];
                }
            }

            $result[] = [
                'id' => $room->id,
                'name' => $room->name,
                'room_type' => $room->roomType->name,
                'default_inventory' => $room->total_inventory,
                'inventory_data' => $dailyData,
            ];
        }

        return $result;
    }

    public function bulkUpdate(int $hotelId, array $data): void
    {
        DB::transaction(function () use ($hotelId, $data) {
            $roomId = $data['room_id'];
            $room = HotelRoom::where('hotel_id', $hotelId)->findOrFail($roomId);

            $dates = CarbonPeriod::create($data['start_date'], $data['end_date']);

            foreach ($dates as $date) {
                $dateString = $date->format('Y-m-d');

                $inventory = HotelInventory::firstOrNew([
                    'room_id' => $roomId,
                    'date' => $dateString,
                ]);

                if (! $inventory->exists) {
                    // Initial setup
                    $inventory->total_inventory = $data['total_inventory'] ?? $room->total_inventory;
                    $inventory->booked = 0;
                    $inventory->blocked = 0;
                    $inventory->held = 0;
                } else {
                    // Update existing
                    if (isset($data['total_inventory'])) {
                        $inventory->total_inventory = $data['total_inventory'];
                    }
                }

                if (isset($data['blocked'])) {
                    // If blocked is boolean or int? Usually "Block 5 rooms".
                    // But here likely "Block this many rooms" OR "Set blocked count".
                    // Let's assume input is "blocked_count".
                    $inventory->blocked = $data['blocked'];
                }

                $inventory->recalculateAvailable();
                $inventory->save();
            }
        });
    }
}
