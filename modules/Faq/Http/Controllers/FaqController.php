<?php

namespace Modules\Faq\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Faq\Http\Requests\FaqValidate;
use Modules\Faq\Models\Faq;
use Modules\Support\Http\Controllers\BackendController;

class FaqController extends BackendController
{
    public function index(): Response
    {
        $faqs = Faq::orderBy('name')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($faq) => [
                'id' => $faq->id,
                'name' => $faq->name,
                'created_at' => $faq->created_at->format('d/m/Y H:i').'h',
            ]);

        return inertia('Faq/FaqIndex', [
            'faqs' => $faqs,
        ]);
    }

    public function create(): Response
    {
        return inertia('Faq/FaqForm');
    }

    public function store(FaqValidate $request): RedirectResponse
    {
        Faq::create($request->validated());

        return redirect()->route('faq.index')
            ->with('success', 'Faq created.');
    }

    public function edit(int $id): Response
    {
        $faq = Faq::find($id);

        return inertia('Faq/FaqForm', [
            'faq' => $faq,
        ]);
    }

    public function update(FaqValidate $request, int $id): RedirectResponse
    {
        $faq = Faq::findOrFail($id);

        $faq->update($request->validated());

        return redirect()->route('faq.index')
            ->with('success', 'Faq updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Faq::findOrFail($id)->delete();

        return redirect()->route('faq.index')
            ->with('success', 'Faq deleted.');
    }
}
