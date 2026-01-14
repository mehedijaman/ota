<?php

namespace Modules\Hotel\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Hotel\Http\Requests\HotelValidate;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Services\GetHotelOptions;
use Modules\Hotel\Services\HotelService;
use Modules\Support\Http\Controllers\BackendController;
use Modules\Support\Traits\EditorImage;

class HotelController extends BackendController
{
    use EditorImage;

    public function __construct(
        protected HotelService $hotelService,
        protected GetHotelOptions $hotelOptions
    ) {}

    public function index(): Response
    {
        return inertia('Hotel/HotelIndex', [
            'hotels' => $this->hotelService->getAll(),
            'stats' => $this->hotelService->getStats(),
            'countries' => $this->hotelOptions->getCountries(),
            'propertyTypes' => $this->hotelOptions->getPropertyTypes(),
            'starRatings' => $this->hotelOptions->getStarRatings(),
        ]);
    }

    public function create(): Response
    {
        return inertia('Hotel/HotelForm', [
            'countries' => $this->hotelOptions->getCountries(),
            'chains' => $this->hotelOptions->getChains(),
            'propertyTypes' => $this->hotelOptions->getPropertyTypes(),
            'starRatings' => $this->hotelOptions->getStarRatings(),
            'amenities' => $this->hotelOptions->getHotelAmenities(),
        ]);
    }

    public function store(HotelValidate $request): RedirectResponse
    {
        $hotel = $this->hotelService->create($request->validated());

        return redirect()->route('hotel.show', $hotel->id)
            ->with('success', 'Hotel created successfully.');
    }

    public function show(int $id): Response
    {
        return inertia('Hotel/HotelShow', [
            'hotel' => $this->hotelService->getById($id),
        ]);
    }

    public function edit(int $id): Response
    {
        return inertia('Hotel/HotelForm', [
            'hotel' => $this->hotelService->getForEdit($id),
            'countries' => $this->hotelOptions->getCountries(),
            'chains' => $this->hotelOptions->getChains(),
            'propertyTypes' => $this->hotelOptions->getPropertyTypes(),
            'starRatings' => $this->hotelOptions->getStarRatings(),
            'amenities' => $this->hotelOptions->getHotelAmenities(),
        ]);
    }

    public function update(HotelValidate $request, int $id): RedirectResponse
    {
        $hotel = Hotel::findOrFail($id);

        $this->hotelService->update($hotel, $request->validated());

        return redirect()->route('hotel.show', $id)
            ->with('success', 'Hotel updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $hotel = Hotel::findOrFail($id);

        if (! $this->hotelService->delete($hotel)) {
            return redirect()->route('hotel.index')
                ->with('error', 'Cannot delete hotel with active bookings.');
        }

        return redirect()->route('hotel.index')
            ->with('success', 'Hotel moved to recycle bin.');
    }

    public function recycleBin(): Response
    {
        return inertia('Hotel/HotelRecycleBin', [
            'hotels' => $this->hotelService->getRecycleBin(),
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        $this->hotelService->restore($id);

        return redirect()->route('hotel.recycleBin')
            ->with('success', 'Hotel restored successfully.');
    }

    public function destroyForce(int $id): RedirectResponse
    {
        $this->hotelService->forceDelete($id);

        return redirect()->route('hotel.recycleBin')
            ->with('success', 'Hotel permanently deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $this->hotelService->emptyRecycleBin();

        return redirect()->route('hotel.recycleBin')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        $this->hotelService->restoreAll();

        return redirect()->route('hotel.recycleBin')
            ->with('success', 'All hotels restored.');
    }

    public function getCities(int $countryId): array
    {
        return $this->hotelOptions->getCities($countryId);
    }
}
