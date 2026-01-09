<?php

namespace Modules\Tour\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Support\Http\Controllers\BackendController;
use Modules\Tour\Http\Requests\TourDestinationValidate;
use Modules\Tour\Models\TourDestination;
use Modules\Tour\Services\TourDestinationService;

class TourDestinationController extends BackendController
{
    public function __construct(
        protected TourDestinationService $destinationService
    ) {}

    public function index(): Response
    {
        return inertia('TourDestination/DestinationIndex', [
            'destinations' => $this->destinationService->getAll(),
        ]);
    }

    public function create(): Response
    {
        return inertia('TourDestination/DestinationForm');
    }

    public function store(TourDestinationValidate $request): RedirectResponse
    {
        $this->destinationService->create($request->validated());

        return redirect()->route('tourDestination.index')
            ->with('success', 'Destination created successfully.');
    }

    public function edit(int $id): Response
    {
        $destination = TourDestination::findOrFail($id);

        return inertia('TourDestination/DestinationForm', [
            'destination' => [
                ...$destination->toArray(),
                'image_url' => $destination->getFirstMediaUrl('image'),
                'gallery' => $destination->getMedia('gallery')->map(fn ($m) => [
                    'id' => $m->id,
                    'url' => $m->getUrl(),
                    'name' => $m->file_name,
                ])->toArray(),
            ],
        ]);
    }

    public function update(TourDestinationValidate $request, int $id): RedirectResponse
    {
        $destination = TourDestination::findOrFail($id);

        $this->destinationService->update($destination, $request->validated());

        return redirect()->route('tourDestination.index')
            ->with('success', 'Destination updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $destination = TourDestination::findOrFail($id);

        if (! $this->destinationService->delete($destination)) {
            return redirect()->route('tourDestination.index')
                ->with('error', 'Cannot delete destination with associated tours.');
        }

        return redirect()->route('tourDestination.index')
            ->with('success', 'Destination deleted successfully.');
    }
}
