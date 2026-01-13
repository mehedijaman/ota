<?php

namespace Modules\Visa\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Support\Http\Controllers\BackendController;
use Modules\Support\Traits\EditorImage;
use Modules\Visa\Http\Requests\VisaValidate;
use Modules\Visa\Models\Visa;
use Modules\Visa\Services\GetVisaCategoryOptions;
use Modules\Visa\Services\GetVisaCountryOptions;
use Modules\Visa\Services\VisaService;

class VisaController extends BackendController
{
    use EditorImage;

    public function __construct(
        protected VisaService $visaService
    ) {}

    public function index(
        GetVisaCountryOptions $countryOptions,
        GetVisaCategoryOptions $categoryOptions
    ): Response {
        return inertia('Visa/Visa/VisaIndex', [
            'visas' => $this->visaService->getAll(),
            'stats' => $this->visaService->getStats(),
            'countries' => $countryOptions->get(),
            'categories' => $categoryOptions->get(),
        ]);
    }

    public function create(
        GetVisaCountryOptions $countryOptions,
        GetVisaCategoryOptions $categoryOptions
    ): Response {
        return inertia('Visa/Visa/VisaForm', [
            'countries' => $countryOptions->get(),
            'categories' => $categoryOptions->get(),
        ]);
    }

    public function store(VisaValidate $request): RedirectResponse
    {
        $this->visaService->create($request->validated());

        return redirect()->route('visa.index')
            ->with('success', 'Visa created successfully.');
    }

    public function show(int $id): Response
    {
        return inertia('Visa/Visa/VisaShow', [
            'visa' => $this->visaService->getById($id),
        ]);
    }

    public function edit(
        GetVisaCountryOptions $countryOptions,
        GetVisaCategoryOptions $categoryOptions,
        int $id
    ): Response {
        return inertia('Visa/Visa/VisaForm', [
            'visa' => $this->visaService->getForEdit($id),
            'countries' => $countryOptions->get(),
            'categories' => $categoryOptions->get(),
        ]);
    }

    public function update(VisaValidate $request, int $id): RedirectResponse
    {
        $visa = Visa::findOrFail($id);

        $this->visaService->update($visa, $request->validated());

        return redirect()->route('visa.index')
            ->with('success', 'Visa updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $visa = Visa::findOrFail($id);

        if (! $this->visaService->delete($visa)) {
            return redirect()->route('visa.index')
                ->with('error', 'Cannot delete visa with existing applications.');
        }

        return redirect()->route('visa.index')
            ->with('success', 'Visa moved to recycle bin.');
    }

    public function recycleBin(): Response
    {
        return inertia('Visa/Visa/VisaRecycleBin', [
            'visas' => $this->visaService->getRecycleBin(),
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        $this->visaService->restore($id);

        return redirect()->route('visa.recycleBin')
            ->with('success', 'Visa restored successfully.');
    }

    public function destroyForce(int $id): RedirectResponse
    {
        $this->visaService->forceDelete($id);

        return redirect()->route('visa.recycleBin')
            ->with('success', 'Visa permanently deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $this->visaService->emptyRecycleBin();

        return redirect()->route('visa.recycleBin')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        $this->visaService->restoreAll();

        return redirect()->route('visa.recycleBin')
            ->with('success', 'All visas restored.');
    }
}
