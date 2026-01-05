<?php

namespace Modules\Sms\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Sms\Http\Requests\SmsValidate;
use Modules\Sms\Models\Sms;
use Modules\Support\Http\Controllers\BackendController;

class SmsController extends BackendController
{
    public function index(): Response
    {
        $sms = Sms::orderBy('name')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($sms) => [
                'id' => $sms->id,
                'name' => $sms->name,
                'created_at' => $sms->created_at->format('d/m/Y H:i').'h',
            ]);

        return inertia('Sms/SmsIndex', [
            'sms' => $sms,
        ]);
    }

    public function create(): Response
    {
        return inertia('Sms/SmsForm');
    }

    public function store(SmsValidate $request): RedirectResponse
    {
        Sms::create($request->validated());

        return redirect()->route('sms.index')
            ->with('success', 'Sms created.');
    }

    public function edit(int $id): Response
    {
        $sms = Sms::find($id);

        return inertia('Sms/SmsForm', [
            'sms' => $sms,
        ]);
    }

    public function update(SmsValidate $request, int $id): RedirectResponse
    {
        $sms = Sms::findOrFail($id);

        $sms->update($request->validated());

        return redirect()->route('sms.index')
            ->with('success', 'Sms updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Sms::findOrFail($id)->delete();

        return redirect()->route('sms.index')
            ->with('success', 'Sms deleted.');
    }
}
