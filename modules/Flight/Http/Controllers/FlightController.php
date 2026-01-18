<?php

namespace Modules\Flight\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Flight\Models\FlightAircraftType;
use Modules\Flight\Models\FlightAirline;
use Modules\Flight\Models\FlightAirport;
use Modules\Flight\Models\FlightFareClass;
use Modules\Flight\Models\FlightSupplier;

class FlightController extends Controller
{
    // ==================== MASTER DATA DASHBOARD ====================

    public function index(): Response
    {
        return Inertia::render('FlightMasterData/FlightMasterDataIndex');
    }

    // ==================== AIRPORTS ====================

    public function airportIndex(): Response
    {
        $airports = FlightAirport::query()
            ->with('country')
            ->when(request('search'), fn ($q, $search) => $q->search($search))
            ->when(request('country_id'), fn ($q, $id) => $q->where('country_id', $id))
            ->when(request('type'), fn ($q, $type) => $q->where('type', $type))
            ->when(request('is_international') !== null, fn ($q) => $q->where('is_international', request('is_international')))
            ->orderBy(request('sort_by', 'name'), request('sort_order', 'asc'))
            ->paginate(request('per_page', 25))
            ->withQueryString();

        return Inertia::render('FlightAirport/FlightAirportIndex', [
            'airports' => $airports,
            'filters' => request()->only(['search', 'country_id', 'type', 'is_international']),
        ]);
    }

    public function airportCreate(): Response
    {
        return Inertia::render('FlightAirport/FlightAirportForm');
    }

    public function airportStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'iata_code' => 'required|string|size:3|unique:flight_airports,iata_code',
            'icao_code' => 'nullable|string|size:4',
            'city' => 'required|string|max:100',
            'country_id' => 'required|exists:countries,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'timezone' => 'nullable|string',
            'type' => 'required|string|in:large_airport,medium_airport,small_airport,heliport,seaplane_base',
            'is_international' => 'boolean',
            'is_active' => 'boolean',
        ]);

        FlightAirport::create($validated);

        return redirect()->route('flight.airport.index')->with('success', 'Airport created successfully.');
    }

    public function airportEdit(int $id): Response
    {
        $airport = FlightAirport::findOrFail($id);

        return Inertia::render('FlightAirport/FlightAirportForm', ['airport' => $airport]);
    }

    public function airportUpdate(Request $request, int $id): RedirectResponse
    {
        $airport = FlightAirport::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'iata_code' => 'required|string|size:3|unique:flight_airports,iata_code,'.$id,
            'icao_code' => 'nullable|string|size:4',
            'city' => 'required|string|max:100',
            'country_id' => 'required|exists:countries,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'timezone' => 'nullable|string',
            'type' => 'required|string',
            'is_international' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $airport->update($validated);

        return redirect()->route('flight.airport.index')->with('success', 'Airport updated successfully.');
    }

    public function airportDestroy(int $id): RedirectResponse
    {
        FlightAirport::findOrFail($id)->delete();

        return redirect()->route('flight.airport.index')->with('success', 'Airport deleted successfully.');
    }

    // ==================== AIRLINES ====================

    public function airlineIndex(): Response
    {
        $airlines = FlightAirline::query()
            ->with('country')
            ->when(request('search'), fn ($q, $search) => $q->search($search))
            ->when(request('alliance'), fn ($q, $alliance) => $q->where('alliance', $alliance))
            ->when(request('type'), fn ($q, $type) => $q->where('type', $type))
            ->orderBy(request('sort_by', 'name'), request('sort_order', 'asc'))
            ->paginate(request('per_page', 25))
            ->withQueryString();

        return Inertia::render('FlightAirline/FlightAirlineIndex', [
            'airlines' => $airlines,
            'filters' => request()->only(['search', 'alliance', 'type']),
        ]);
    }

    public function airlineCreate(): Response
    {
        return Inertia::render('FlightAirline/FlightAirlineForm');
    }

    public function airlineStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'iata_code' => 'required|string|size:2|unique:flight_airlines,iata_code',
            'icao_code' => 'nullable|string|size:3',
            'country_id' => 'required|exists:countries,id',
            'type' => 'required|in:scheduled,charter,low_cost,cargo,regional',
            'alliance' => 'nullable|in:none,star_alliance,oneworld,skyteam',
            'is_active' => 'boolean',
        ]);

        FlightAirline::create($validated);

        return redirect()->route('flight.airline.index')->with('success', 'Airline created successfully.');
    }

    public function airlineEdit(int $id): Response
    {
        $airline = FlightAirline::findOrFail($id);

        return Inertia::render('FlightAirline/FlightAirlineForm', ['airline' => $airline]);
    }

    public function airlineUpdate(Request $request, int $id): RedirectResponse
    {
        $airline = FlightAirline::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'iata_code' => 'required|string|size:2|unique:flight_airlines,iata_code,'.$id,
            'icao_code' => 'nullable|string|size:3',
            'country_id' => 'required|exists:countries,id',
            'type' => 'required|in:scheduled,charter,low_cost,cargo,regional',
            'alliance' => 'nullable|in:none,star_alliance,oneworld,skyteam',
            'is_active' => 'boolean',
        ]);

        $airline->update($validated);

        return redirect()->route('flight.airline.index')->with('success', 'Airline updated successfully.');
    }

    public function airlineDestroy(int $id): RedirectResponse
    {
        FlightAirline::findOrFail($id)->delete();

        return redirect()->route('flight.airline.index')->with('success', 'Airline deleted successfully.');
    }

    // ==================== AIRCRAFT TYPES ====================

    public function aircraftIndex(): Response
    {
        $aircraft = FlightAircraftType::query()
            ->when(request('search'), fn ($q, $search) => $q->where('name', 'like', "%{$search}%")->orWhere('manufacturer', 'like', "%{$search}%"))
            ->when(request('manufacturer'), fn ($q, $m) => $q->where('manufacturer', $m))
            ->when(request('category'), fn ($q, $c) => $q->where('category', $c))
            ->orderBy(request('sort_by', 'name'), request('sort_order', 'asc'))
            ->paginate(request('per_page', 25))
            ->withQueryString();

        return Inertia::render('Flight/Aircraft/Index', [
            'aircraft' => $aircraft,
            'filters' => request()->only(['search', 'manufacturer', 'category']),
        ]);
    }

    // ==================== SUPPLIERS ====================

    public function supplierIndex(): Response
    {
        $suppliers = FlightSupplier::query()
            ->when(request('search'), fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when(request('is_active') !== null, fn ($q) => $q->where('is_active', request('is_active')))
            ->orderBy('name')
            ->paginate(request('per_page', 25))
            ->withQueryString();

        return Inertia::render('Flight/Suppliers/Index', ['suppliers' => $suppliers]);
    }

    // ==================== FARE CLASSES ====================

    public function fareClassIndex(): Response
    {
        $fareClasses = FlightFareClass::query()
            ->with('airline')
            ->when(request('search'), fn ($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('code', 'like', "%{$s}%"))
            ->when(request('airline_id'), fn ($q, $id) => $q->where('airline_id', $id))
            ->when(request('cabin_class'), fn ($q, $c) => $q->where('cabin_class', $c))
            ->orderBy('airline_id')->orderBy('display_order')
            ->paginate(request('per_page', 25))
            ->withQueryString();

        return Inertia::render('Flight/FareClasses/Index', ['fareClasses' => $fareClasses]);
    }

    // ==================== API ENDPOINTS ====================

    public function apiSearchAirports(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $airports = FlightAirport::query()
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('iata_code', 'like', "{$query}%")
                    ->orWhere('name', 'like', "%{$query}%")
                    ->orWhere('city', 'like', "%{$query}%");
            })
            ->orderByDesc('is_popular')
            ->orderByDesc('search_priority')
            ->limit(10)
            ->get(['id', 'uuid', 'iata_code', 'name', 'city', 'country_id']);

        return response()->json($airports);
    }

    public function apiSearchAirlines(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $airlines = FlightAirline::query()
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('iata_code', 'like', "{$query}%")
                    ->orWhere('name', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'uuid', 'iata_code', 'name']);

        return response()->json($airlines);
    }
}
