<?php

namespace Modules\Tour\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Support\Http\Controllers\BackendController;
use Modules\Tour\Http\Requests\TourValidate;
use Modules\Tour\Models\Tour;

class TourController extends BackendController
{
    public function index(): Response
    {
        $tours = Tour::orderBy('name')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($tour) => [
                'id' => $tour->id,
                'name' => $tour->name,
                'created_at' => $tour->created_at->format('d/m/Y H:i').'h',
            ]);

        return inertia('Tour/TourIndex', [
            'tours' => $tours,
        ]);
    }

    public function create(): Response
    {
        return inertia('Tour/TourForm');
    }

    public function store(TourValidate $request): RedirectResponse
    {
        Tour::create($request->validated());

        return redirect()->route('tour.index')
            ->with('success', 'Tour created.');
    }

    public function edit(int $id): Response
    {
        $tour = Tour::find($id);

        return inertia('Tour/TourForm', [
            'tour' => $tour,
        ]);
    }

    public function update(TourValidate $request, int $id): RedirectResponse
    {
        $tour = Tour::findOrFail($id);

        $tour->update($request->validated());

        return redirect()->route('tour.index')
            ->with('success', 'Tour updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Tour::findOrFail($id)->delete();

        return redirect()->route('tour.index')
            ->with('success', 'Tour deleted.');
    }
}
