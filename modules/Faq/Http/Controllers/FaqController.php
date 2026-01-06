<?php

namespace Modules\Faq\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Faq\Http\Requests\FaqValidate;
use Modules\Faq\Models\Faq;
use Modules\Faq\Services\FaqCategoryService;
use Modules\Faq\Services\FaqService;
use Modules\Support\Http\Controllers\BackendController;

class FaqController extends BackendController
{
    public function __construct(
        public FaqService $service,
        public FaqCategoryService $categoryService
    ) {}

    public function index(): Response
    {
        return inertia('Faq/FaqIndex', [
            'faqs' => $this->service->getAll(),
        ]);
    }

    public function create(): Response
    {
        return inertia('Faq/FaqForm', [
            'categories' => $this->categoryService->getOptions(),
        ]);
    }

    public function store(FaqValidate $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('faq.index')
            ->with('success', 'FAQ created.');
    }

    public function show(Faq $faq): Response
    {
        $faq->load('category:id,name');

        return inertia('Faq/FaqShow', [
            'faq' => $faq,
        ]);
    }

    public function edit(Faq $faq): Response
    {
        return inertia('Faq/FaqForm', [
            'faq' => $faq,
            'categories' => $this->categoryService->getOptions(),
        ]);
    }

    public function update(FaqValidate $request, Faq $faq): RedirectResponse
    {
        $this->service->update($faq, $request->validated());

        return redirect()->route('faq.index')
            ->with('success', 'FAQ updated.');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $this->service->delete($faq);

        return redirect()->route('faq.index')
            ->with('success', 'FAQ deleted.');
    }

    public function recycleBin(): Response
    {
        return inertia('Faq/FaqRecycleBin', [
            'faqs' => $this->service->getRecycleBin(),
        ]);
    }

    public function restore(string $uuid): RedirectResponse
    {
        $this->service->restore($uuid);

        return redirect()->route('faq.recycleBin')
            ->with('success', 'FAQ restored.');
    }

    public function destroyForce(string $uuid): RedirectResponse
    {
        $this->service->forceDelete($uuid);

        return redirect()->route('faq.recycleBin')
            ->with('success', 'FAQ permanently deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $count = $this->service->emptyRecycleBin();

        return redirect()->route('faq.recycleBin')
            ->with('success', "{$count} FAQs permanently deleted.");
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        $count = $this->service->restoreAll();

        return redirect()->route('faq.recycleBin')
            ->with('success', "{$count} FAQs restored.");
    }
}
