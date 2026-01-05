<?php

namespace Modules\Bill\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Bill\Http\Requests\BillValidate;
use Modules\Bill\Models\Bill;
use Modules\Support\Http\Controllers\BackendController;

class BillController extends BackendController
{
    public function index(): Response
    {
        $bills = Bill::orderBy('name')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($bill) => [
                'id' => $bill->id,
                'name' => $bill->name,
                'created_at' => $bill->created_at->format('d/m/Y H:i').'h',
            ]);

        return inertia('Bill/BillIndex', [
            'bills' => $bills,
        ]);
    }

    public function create(): Response
    {
        return inertia('Bill/BillForm');
    }

    public function store(BillValidate $request): RedirectResponse
    {
        Bill::create($request->validated());

        return redirect()->route('bill.index')
            ->with('success', 'Bill created.');
    }

    public function edit(int $id): Response
    {
        $bill = Bill::find($id);

        return inertia('Bill/BillForm', [
            'bill' => $bill,
        ]);
    }

    public function update(BillValidate $request, int $id): RedirectResponse
    {
        $bill = Bill::findOrFail($id);

        $bill->update($request->validated());

        return redirect()->route('bill.index')
            ->with('success', 'Bill updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Bill::findOrFail($id)->delete();

        return redirect()->route('bill.index')
            ->with('success', 'Bill deleted.');
    }
}
