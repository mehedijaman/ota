<?php

namespace Modules\Faq\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Faq\Http\Requests\FaqCategoryValidate;
use Modules\Faq\Models\FaqCategory;
use Modules\Faq\Services\FaqCategoryService;
use Modules\Support\Http\Controllers\BackendController;

class FaqCategoryController extends BackendController
{
    public function __construct(public FaqCategoryService $service) {}

    public function index(): Response
    {
        return inertia('FaqCategory/FaqCategoryIndex', [
            'categories' => $this->service->getAll(),
        ]);
    }

    public function create(): Response
    {
        return inertia('FaqCategory/FaqCategoryForm');
    }

    public function store(FaqCategoryValidate $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('faqCategory.index')
            ->with('success', 'FAQ Category created.');
    }

    public function edit(FaqCategory $faqCategory): Response
    {
        return inertia('FaqCategory/FaqCategoryForm', [
            'category' => $faqCategory,
        ]);
    }

    public function update(FaqCategoryValidate $request, FaqCategory $faqCategory): RedirectResponse
    {
        $this->service->update($faqCategory, $request->validated());

        return redirect()->route('faqCategory.index')
            ->with('success', 'FAQ Category updated.');
    }

    public function destroy(FaqCategory $faqCategory): RedirectResponse
    {
        $this->service->delete($faqCategory);

        return redirect()->route('faqCategory.index')
            ->with('success', 'FAQ Category deleted.');
    }

    public function recycleBin(): Response
    {
        return inertia('FaqCategory/FaqCategoryRecycleBin', [
            'categories' => $this->service->getRecycleBin(),
        ]);
    }

    public function restore(string $uuid): RedirectResponse
    {
        $this->service->restore($uuid);

        return redirect()->route('faqCategory.recycleBin')
            ->with('success', 'FAQ Category restored.');
    }

    public function destroyForce(string $uuid): RedirectResponse
    {
        $this->service->forceDelete($uuid);

        return redirect()->route('faqCategory.recycleBin')
            ->with('success', 'FAQ Category permanently deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $count = $this->service->emptyRecycleBin();

        return redirect()->route('faqCategory.recycleBin')
            ->with('success', "{$count} FAQ Categories permanently deleted.");
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        $count = $this->service->restoreAll();

        return redirect()->route('faqCategory.recycleBin')
            ->with('success', "{$count} FAQ Categories restored.");
    }
}
