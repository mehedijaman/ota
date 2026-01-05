<?php

namespace Modules\Medical\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Medical\Http\Requests\MedicalValidate;
use Modules\Medical\Models\Medical;
use Modules\Support\Http\Controllers\BackendController;

class MedicalController extends BackendController
{
    public function index(): Response
    {
        $medicals = Medical::orderBy('name')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($medical) => [
                'id' => $medical->id,
                'name' => $medical->name,
                'created_at' => $medical->created_at->format('d/m/Y H:i').'h',
            ]);

        return inertia('Medical/MedicalIndex', [
            'medicals' => $medicals,
        ]);
    }

    public function create(): Response
    {
        return inertia('Medical/MedicalForm');
    }

    public function store(MedicalValidate $request): RedirectResponse
    {
        Medical::create($request->validated());

        return redirect()->route('medical.index')
            ->with('success', 'Medical created.');
    }

    public function edit(int $id): Response
    {
        $medical = Medical::find($id);

        return inertia('Medical/MedicalForm', [
            'medical' => $medical,
        ]);
    }

    public function update(MedicalValidate $request, int $id): RedirectResponse
    {
        $medical = Medical::findOrFail($id);

        $medical->update($request->validated());

        return redirect()->route('medical.index')
            ->with('success', 'Medical updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Medical::findOrFail($id)->delete();

        return redirect()->route('medical.index')
            ->with('success', 'Medical deleted.');
    }
}
