<?php

namespace Modules\Hotel\Http\Controllers;

use Modules\Support\Http\Controllers\BackendController;
use Modules\Hotel\Http\Requests\HotelValidate;
use Modules\Hotel\Models\Hotel;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class HotelController extends BackendController
{
    public function index(): Response
    {
        $hotels = Hotel::orderBy('name')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($hotel) => [
                    'id' => $hotel->id,
                    'name' => $hotel->name,
                    'created_at' => $hotel->created_at->format('d/m/Y H:i') . 'h'
            ]);

        return inertia('Hotel/HotelIndex', [
            'hotels' => $hotels
        ]);
    }

    public function create(): Response
    {
        return inertia('Hotel/HotelForm');
    }

    public function store(HotelValidate $request): RedirectResponse
    {
        Hotel::create($request->validated());

        return redirect()->route('hotel.index')
            ->with('success', 'Hotel created.');
    }

    public function edit(int $id): Response
    {
        $hotel = Hotel::find($id);

        return inertia('Hotel/HotelForm', [
            'hotel' => $hotel
        ]);
    }

    public function update(HotelValidate $request, int $id): RedirectResponse
    {
        $hotel = Hotel::findOrFail($id);

        $hotel->update($request->validated());

        return redirect()->route('hotel.index')
            ->with('success', 'Hotel updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Hotel::findOrFail($id)->delete();

        return redirect()->route('hotel.index')
            ->with('success', 'Hotel deleted.');
    }
}
