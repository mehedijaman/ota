<?php

namespace Modules\Flight\Http\Controllers;

use Modules\Support\Http\Controllers\BackendController;
use Modules\Flight\Http\Requests\FlightValidate;
use Modules\Flight\Models\Flight;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class FlightController extends BackendController
{
    public function index(): Response
    {
        $flights = Flight::orderBy('name')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($flight) => [
                    'id' => $flight->id,
                    'name' => $flight->name,
                    'created_at' => $flight->created_at->format('d/m/Y H:i') . 'h'
            ]);

        return inertia('Flight/FlightIndex', [
            'flights' => $flights
        ]);
    }

    public function create(): Response
    {
        return inertia('Flight/FlightForm');
    }

    public function store(FlightValidate $request): RedirectResponse
    {
        Flight::create($request->validated());

        return redirect()->route('flight.index')
            ->with('success', 'Flight created.');
    }

    public function edit(int $id): Response
    {
        $flight = Flight::find($id);

        return inertia('Flight/FlightForm', [
            'flight' => $flight
        ]);
    }

    public function update(FlightValidate $request, int $id): RedirectResponse
    {
        $flight = Flight::findOrFail($id);

        $flight->update($request->validated());

        return redirect()->route('flight.index')
            ->with('success', 'Flight updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Flight::findOrFail($id)->delete();

        return redirect()->route('flight.index')
            ->with('success', 'Flight deleted.');
    }
}
