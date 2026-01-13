<?php

namespace Modules\Visa\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Support\Http\Controllers\BackendController;
use Modules\Visa\Http\Requests\VisaCategoryValidate;
use Modules\Visa\Models\VisaCategory;
use Modules\Visa\Services\VisaCategoryService;

class VisaCategoryController extends BackendController
{
    public function __construct(
        protected VisaCategoryService $categoryService
    ) {}

    public function index(): Response
    {
        return inertia('Visa/Category/CategoryIndex', [
            'categories' => $this->categoryService->getAll(),
            'stats' => $this->categoryService->getStats(),
        ]);
    }

    public function create(): Response
    {
        return inertia('Visa/Category/CategoryForm');
    }

    public function store(VisaCategoryValidate $request): RedirectResponse
    {
        $this->categoryService->create($request->validated());

        return redirect()->route('visaCategory.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(int $id): Response
    {
        return inertia('Visa/Category/CategoryForm', [
            'category' => $this->categoryService->getForEdit($id),
        ]);
    }

    public function update(VisaCategoryValidate $request, int $id): RedirectResponse
    {
        $category = VisaCategory::findOrFail($id);

        $this->categoryService->update($category, $request->validated());

        return redirect()->route('visaCategory.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $category = VisaCategory::findOrFail($id);

        if (! $this->categoryService->delete($category)) {
            return redirect()->route('visaCategory.index')
                ->with('error', 'Cannot delete category with existing visas.');
        }

        return redirect()->route('visaCategory.index')
            ->with('success', 'Category moved to recycle bin.');
    }

    public function recycleBin(): Response
    {
        return inertia('Visa/Category/CategoryRecycleBin', [
            'categories' => $this->categoryService->getRecycleBin(),
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        $this->categoryService->restore($id);

        return redirect()->route('visaCategory.recycleBin')
            ->with('success', 'Category restored successfully.');
    }

    public function destroyForce(int $id): RedirectResponse
    {
        $this->categoryService->forceDelete($id);

        return redirect()->route('visaCategory.recycleBin')
            ->with('success', 'Category permanently deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $this->categoryService->emptyRecycleBin();

        return redirect()->route('visaCategory.recycleBin')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        $this->categoryService->restoreAll();

        return redirect()->route('visaCategory.recycleBin')
            ->with('success', 'All categories restored.');
    }
}
