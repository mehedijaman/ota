<?php

namespace Modules\Visa\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Support\Http\Controllers\BackendController;
use Modules\Visa\Http\Requests\VisaCountryValidate;
use Modules\Visa\Models\VisaCountry;
use Modules\Visa\Services\VisaCountryService;

class VisaCountryController extends BackendController
{
    public function __construct(
        protected VisaCountryService $countryService
    ) {}

    public function index(): Response
    {
        return inertia('Visa/Country/CountryIndex', [
            'countries' => $this->countryService->getAll(),
            'stats' => $this->countryService->getStats(),
        ]);
    }

    public function create(): Response
    {
        return inertia('Visa/Country/CountryForm');
    }

    public function store(VisaCountryValidate $request): RedirectResponse
    {
        $this->countryService->create($request->validated());

        return redirect()->route('visaCountry.index')
            ->with('success', 'Country created successfully.');
    }

    public function show(int $id): Response
    {
        return inertia('Visa/Country/CountryShow', [
            'country' => $this->countryService->getById($id),
        ]);
    }

    public function edit(int $id): Response
    {
        return inertia('Visa/Country/CountryForm', [
            'country' => $this->countryService->getForEdit($id),
        ]);
    }

    public function update(VisaCountryValidate $request, int $id): RedirectResponse
    {
        $country = VisaCountry::findOrFail($id);

        $this->countryService->update($country, $request->validated());

        return redirect()->route('visaCountry.index')
            ->with('success', 'Country updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $country = VisaCountry::findOrFail($id);

        if (! $this->countryService->delete($country)) {
            return redirect()->route('visaCountry.index')
                ->with('error', 'Cannot delete country with existing visas.');
        }

        return redirect()->route('visaCountry.index')
            ->with('success', 'Country moved to recycle bin.');
    }

    public function recycleBin(): Response
    {
        return inertia('Visa/Country/CountryRecycleBin', [
            'countries' => $this->countryService->getRecycleBin(),
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        $this->countryService->restore($id);

        return redirect()->route('visaCountry.recycleBin')
            ->with('success', 'Country restored successfully.');
    }

    public function destroyForce(int $id): RedirectResponse
    {
        $this->countryService->forceDelete($id);

        return redirect()->route('visaCountry.recycleBin')
            ->with('success', 'Country permanently deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $this->countryService->emptyRecycleBin();

        return redirect()->route('visaCountry.recycleBin')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        $this->countryService->restoreAll();

        return redirect()->route('visaCountry.recycleBin')
            ->with('success', 'All countries restored.');
    }
}
