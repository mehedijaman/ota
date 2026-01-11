<?php

namespace Modules\Tour\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Support\Http\Controllers\BackendController;
use Modules\Support\Traits\EditorImage;
use Modules\Tour\Http\Requests\TourValidate;
use Modules\Tour\Models\Tour;
use Modules\Tour\Services\GetTourCategoryOptions;
use Modules\Tour\Services\GetTourDestinationOptions;
use Modules\Tour\Services\TourService;

class TourController extends BackendController
{
    use EditorImage;

    public function __construct(
        protected TourService $tourService
    ) {}

    public function index(
        GetTourCategoryOptions $categoryOptions,
        GetTourDestinationOptions $destinationOptions
    ): Response {
        return inertia('Tour/TourIndex', [
            'tours' => $this->tourService->getAll(),
            'categories' => $categoryOptions->get(),
            'destinations' => $destinationOptions->get(),
        ]);
    }

    public function create(
        GetTourCategoryOptions $categoryOptions,
        GetTourDestinationOptions $destinationOptions
    ): Response {
        return inertia('Tour/TourForm', [
            'categories' => $categoryOptions->get(),
            'destinations' => $destinationOptions->get(),
        ]);
    }

    public function store(TourValidate $request): RedirectResponse
    {
        $this->tourService->create($request->validated());

        return redirect()->route('tour.index')
            ->with('success', 'Tour created successfully.');
    }

    public function show(int $id): Response
    {
        return inertia('Tour/TourShow', [
            'tour' => $this->tourService->getById($id),
        ]);
    }

    public function edit(
        GetTourCategoryOptions $categoryOptions,
        GetTourDestinationOptions $destinationOptions,
        int $id
    ): Response {
        return inertia('Tour/TourForm', [
            'tour' => $this->tourService->getForEdit($id),
            'categories' => $categoryOptions->get(),
            'destinations' => $destinationOptions->get(),
        ]);
    }

    public function update(TourValidate $request, int $id): RedirectResponse
    {
        $tour = Tour::findOrFail($id);

        $this->tourService->update($tour, $request->validated());

        return redirect()->route('tour.index')
            ->with('success', 'Tour updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $tour = Tour::findOrFail($id);

        if (! $this->tourService->delete($tour)) {
            return redirect()->route('tour.index')
                ->with('error', 'Cannot delete tour with existing bookings.');
        }

        return redirect()->route('tour.index')
            ->with('success', 'Tour moved to recycle bin.');
    }

    public function recycleBin(): Response
    {
        return inertia('Tour/TourRecycleBin', [
            'tours' => $this->tourService->getRecycleBin(),
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        $this->tourService->restore($id);

        return redirect()->route('tour.recycleBin')
            ->with('success', 'Tour restored successfully.');
    }

    public function destroyForce(int $id): RedirectResponse
    {
        $this->tourService->forceDelete($id);

        return redirect()->route('tour.recycleBin')
            ->with('success', 'Tour permanently deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $this->tourService->emptyRecycleBin();

        return redirect()->route('tour.recycleBin')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        $this->tourService->restoreAll();

        return redirect()->route('tour.recycleBin')
            ->with('success', 'All tours restored.');
    }
}
