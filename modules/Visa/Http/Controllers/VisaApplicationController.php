<?php

namespace Modules\Visa\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Support\Http\Controllers\BackendController;
use Modules\Visa\Http\Requests\VisaApplicationPaymentValidate;
use Modules\Visa\Http\Requests\VisaApplicationValidate;
use Modules\Visa\Models\VisaApplication;
use Modules\Visa\Services\GetVisaCountryOptions;
use Modules\Visa\Services\GetVisaOptions;
use Modules\Visa\Services\VisaApplicationService;

class VisaApplicationController extends BackendController
{
    public function __construct(
        protected VisaApplicationService $applicationService
    ) {}

    public function index(GetVisaCountryOptions $countryOptions): Response
    {
        return inertia('Visa/Application/ApplicationIndex', [
            'applications' => $this->applicationService->getAll(),
            'stats' => $this->applicationService->getStats(),
            'countries' => $countryOptions->get(),
        ]);
    }

    public function create(GetVisaOptions $visaOptions): Response
    {
        $customers = \Modules\Customer\Models\Customer::select('id', 'first_name', 'last_name', 'email', 'phone', 'whatsapp', 'address')
            ->orderBy('first_name')
            ->get()
            ->map(fn ($customer) => [
                'id' => $customer->id,
                'name' => $customer->full_name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'whatsapp' => $customer->whatsapp,
                'address' => $customer->address,
            ]);

        return inertia('Visa/Application/ApplicationForm', [
            'visas' => $visaOptions->getWithPricing(),
            'customers' => $customers,
            'agents' => \Modules\Agent\Models\Agent::select('id', 'name', 'email', 'phone', 'whatsapp', 'address')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(VisaApplicationValidate $request): RedirectResponse
    {
        $app = $this->applicationService->create($request->validated());

        return redirect()->route('visaApplication.show', $app->id)
            ->with('success', 'Application created successfully. Application #'.$app->application_number);
    }

    public function show(int $id): Response
    {
        return inertia('Visa/Application/ApplicationShow', [
            'application' => $this->applicationService->getById($id),
        ]);
    }

    public function edit(int $id, GetVisaOptions $visaOptions): Response
    {
        $customers = \Modules\Customer\Models\Customer::select('id', 'first_name', 'last_name', 'email', 'phone', 'whatsapp', 'address')
            ->orderBy('first_name')
            ->get()
            ->map(fn ($customer) => [
                'id' => $customer->id,
                'name' => $customer->full_name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'whatsapp' => $customer->whatsapp,
                'address' => $customer->address,
            ]);

        return inertia('Visa/Application/ApplicationForm', [
            'application' => $this->applicationService->getForEdit($id),
            'visas' => $visaOptions->getWithPricing(),
            'customers' => $customers,
            'agents' => \Modules\Agent\Models\Agent::select('id', 'name', 'email', 'phone', 'whatsapp', 'address')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(VisaApplicationValidate $request, int $id): RedirectResponse
    {
        $app = VisaApplication::findOrFail($id);

        $this->applicationService->update($app, $request->validated());

        return redirect()->route('visaApplication.show', $id)
            ->with('success', 'Application updated successfully.');
    }

    public function updateStatus(int $id, string $status): RedirectResponse
    {
        $app = VisaApplication::findOrFail($id);
        $note = request('note');

        $this->applicationService->updateStatus($app, $status, $note);

        return redirect()->route('visaApplication.show', $id)
            ->with('success', 'Application status updated successfully.');
    }

    public function addPayment(VisaApplicationPaymentValidate $request, int $id): RedirectResponse
    {
        $app = VisaApplication::findOrFail($id);

        $this->applicationService->addPayment($app, $request->validated());

        return redirect()->route('visaApplication.show', $id)
            ->with('success', 'Payment added successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $app = VisaApplication::findOrFail($id);

        if (! $this->applicationService->delete($app)) {
            return redirect()->route('visaApplication.index')
                ->with('error', 'Cannot delete application in processing or completed status.');
        }

        return redirect()->route('visaApplication.index')
            ->with('success', 'Application moved to recycle bin.');
    }

    public function recycleBin(): Response
    {
        return inertia('Visa/Application/ApplicationRecycleBin', [
            'applications' => $this->applicationService->getRecycleBin(),
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        $this->applicationService->restore($id);

        return redirect()->route('visaApplication.recycleBin')
            ->with('success', 'Application restored successfully.');
    }

    public function destroyForce(int $id): RedirectResponse
    {
        $this->applicationService->forceDelete($id);

        return redirect()->route('visaApplication.recycleBin')
            ->with('success', 'Application permanently deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $this->applicationService->emptyRecycleBin();

        return redirect()->route('visaApplication.recycleBin')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        $this->applicationService->restoreAll();

        return redirect()->route('visaApplication.recycleBin')
            ->with('success', 'All applications restored.');
    }
}
