<?php

namespace Modules\Tour\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Support\Http\Controllers\BackendController;
use Modules\Tour\Http\Requests\TourCategoryValidate;
use Modules\Tour\Models\TourCategory;
use Modules\Tour\Services\TourCategoryService;

class TourCategoryController extends BackendController
{
    public function __construct(
        protected TourCategoryService $categoryService
    ) {}

    public function index(): Response
    {
        return inertia('TourCategory/CategoryIndex', [
            'categories' => $this->categoryService->getAll(),
        ]);
    }

    public function create(): Response
    {
        return inertia('TourCategory/CategoryForm');
    }

    public function store(TourCategoryValidate $request): RedirectResponse
    {
        $this->categoryService->create($request->validated());

        return redirect()->route('tourCategory.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(int $id): Response
    {
        $category = TourCategory::findOrFail($id);

        return inertia('TourCategory/CategoryForm', [
            'category' => [
                ...$category->toArray(),
                'image_url' => $category->getFirstMediaUrl('image'),
            ],
        ]);
    }

    public function update(TourCategoryValidate $request, int $id): RedirectResponse
    {
        $category = TourCategory::findOrFail($id);

        $this->categoryService->update($category, $request->validated());

        return redirect()->route('tourCategory.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $category = TourCategory::findOrFail($id);

        if (! $this->categoryService->delete($category)) {
            return redirect()->route('tourCategory.index')
                ->with('error', 'Cannot delete category with associated tours.');
        }

        return redirect()->route('tourCategory.index')
            ->with('success', 'Category deleted successfully.');
    }

    public function recycleBin(): Response
    {
        return inertia('TourCategory/CategoryRecycleBin', [
            'categories' => $this->categoryService->getRecycleBin(),
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        $this->categoryService->restore($id);

        return redirect()->route('tourCategory.recycleBin')
            ->with('success', 'Category restored successfully.');
    }

    public function destroyForce(int $id): RedirectResponse
    {
        $this->categoryService->forceDelete($id);

        return redirect()->route('tourCategory.recycleBin')
            ->with('success', 'Category permanently deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $this->categoryService->emptyRecycleBin();

        return redirect()->route('tourCategory.recycleBin')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        $this->categoryService->restoreAll();

        return redirect()->route('tourCategory.recycleBin')
            ->with('success', 'All categories restored.');
    }
}
