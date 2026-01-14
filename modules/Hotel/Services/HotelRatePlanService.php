<?php

namespace Modules\Hotel\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\Hotel\Models\HotelRatePlan;

class HotelRatePlanService
{
    public function getAllForHotel(int $hotelId): LengthAwarePaginator
    {
        return HotelRatePlan::query()
            ->where('hotel_id', $hotelId)
            ->with(['cancellationPolicy'])
            ->when(request('search'), fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->when(request('meal_plan'), fn ($q, $plan) => $q->where('meal_plan', $plan))
            ->when(request('status') === 'active', fn ($q) => $q->active())
            ->when(request('status') === 'inactive', fn ($q) => $q->where('is_active', false))
            ->orderBy(request('sort_by', 'sort_order'), request('sort_order', 'asc'))
            ->paginate(request('per_page', 15))
            ->withQueryString();
    }

    public function getById(int $id): HotelRatePlan
    {
        return HotelRatePlan::with(['hotel', 'cancellationPolicy', 'rates'])
            ->findOrFail($id);
    }

    public function create(array $data): HotelRatePlan
    {
        return DB::transaction(function () use ($data) {
            $ratePlan = HotelRatePlan::create($data);

            // If marked as default, unset other defaults for this hotel
            if ($ratePlan->is_default) {
                $this->clearOtherDefaults($ratePlan);
            }

            return $ratePlan;
        });
    }

    public function update(HotelRatePlan $ratePlan, array $data): HotelRatePlan
    {
        return DB::transaction(function () use ($ratePlan, $data) {
            $ratePlan->update($data);

            if ($ratePlan->is_default) {
                $this->clearOtherDefaults($ratePlan);
            }

            return $ratePlan->fresh();
        });
    }

    protected function clearOtherDefaults(HotelRatePlan $ratePlan): void
    {
        HotelRatePlan::where('hotel_id', $ratePlan->hotel_id)
            ->where('id', '!=', $ratePlan->id)
            ->where('is_default', true)
            ->update(['is_default' => false]);
    }

    public function delete(HotelRatePlan $ratePlan): bool
    {
        if ($ratePlan->bookingRooms()->exists()) {
            return false;
        }

        $ratePlan->delete();

        return true;
    }

    public function getRecycleBin(int $hotelId): LengthAwarePaginator
    {
        return HotelRatePlan::onlyTrashed()
            ->where('hotel_id', $hotelId)
            ->orderByDesc('deleted_at')
            ->paginate(15)
            ->withQueryString();
    }

    public function restore(int $id): void
    {
        HotelRatePlan::onlyTrashed()->findOrFail($id)->restore();
    }

    public function forceDelete(int $id): void
    {
        HotelRatePlan::onlyTrashed()->findOrFail($id)->forceDelete();
    }

    public function getMealPlanOptions(): array
    {
        return [
            ['value' => 'room_only', 'label' => 'Room Only'],
            ['value' => 'breakfast', 'label' => 'Breakfast Included'],
            ['value' => 'half_board', 'label' => 'Half Board'],
            ['value' => 'full_board', 'label' => 'Full Board'],
            ['value' => 'all_inclusive', 'label' => 'All Inclusive'],
        ];
    }
}
