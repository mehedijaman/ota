<?php

namespace Modules\Visa\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Support\Http\Controllers\BackendController;
use Modules\Visa\Http\Requests\VisaValidate;
use Modules\Visa\Models\Visa;

class VisaController extends BackendController
{
    public function index(): Response
    {
        $visas = Visa::orderBy('name')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($visa) => [
                'id' => $visa->id,
                'name' => $visa->name,
                'created_at' => $visa->created_at->format('d/m/Y H:i').'h',
            ]);

        return inertia('Visa/VisaIndex', [
            'visas' => $visas,
        ]);
    }

    public function create(): Response
    {
        return inertia('Visa/VisaForm');
    }

    public function store(VisaValidate $request): RedirectResponse
    {
        Visa::create($request->validated());

        return redirect()->route('visa.index')
            ->with('success', 'Visa created.');
    }

    public function edit(int $id): Response
    {
        $visa = Visa::find($id);

        return inertia('Visa/VisaForm', [
            'visa' => $visa,
        ]);
    }

    public function update(VisaValidate $request, int $id): RedirectResponse
    {
        $visa = Visa::findOrFail($id);

        $visa->update($request->validated());

        return redirect()->route('visa.index')
            ->with('success', 'Visa updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Visa::findOrFail($id)->delete();

        return redirect()->route('visa.index')
            ->with('success', 'Visa deleted.');
    }
}
