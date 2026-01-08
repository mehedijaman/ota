<?php

namespace Modules\Page\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Page\Http\Requests\PageValidate;
use Modules\Page\Models\Page;
use Modules\Page\Services\PageService;
use Modules\Support\Http\Controllers\BackendController;
use Modules\Support\Traits\EditorImage;

class PageController extends BackendController
{
    use EditorImage;

    protected string $uploadImagePath = 'page-editor-files';

    public function __construct(
        protected PageService $pageService
    ) {}

    public function index(): Response
    {
        return inertia('Page/PageIndex', [
            'pages' => $this->pageService->getAll(),
        ]);
    }

    public function create(): Response
    {
        return inertia('Page/PageForm');
    }

    public function store(PageValidate $request): RedirectResponse
    {
        try {
            $page = $this->pageService->create($request->validated());

            return redirect()->route('page.edit', $page)
                ->with('success', 'Page created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create page: '.$e->getMessage());
        }
    }

    public function edit(int $id): Response
    {
        $page = Page::with(['parent'])->findOrFail($id);

        return inertia('Page/PageForm', [
            'page' => $page,
        ]);
    }

    public function update(PageValidate $request, int $id): RedirectResponse
    {
        try {
            $page = Page::findOrFail($id);
            $this->pageService->update($page, $request->validated());

            return redirect()->route('page.index')
                ->with('success', 'Page updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update page: '.$e->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $page = Page::findOrFail($id);
            $this->pageService->delete($page);

            return redirect()->route('page.index')
                ->with('success', 'Page deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete page: '.$e->getMessage());
        }
    }

    public function recycleBin(): Response
    {
        return inertia('Page/PageRecycleBin', [
            'pages' => $this->pageService->getRecycleBin(),
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        try {
            $this->pageService->restore($id);

            return redirect()->route('page.recycleBin.index')
                ->with('success', 'Page restored successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to restore page: '.$e->getMessage());
        }
    }

    public function destroyForce(int $id): RedirectResponse
    {
        try {
            $this->pageService->forceDelete($id);

            return redirect()->route('page.recycleBin.index')
                ->with('success', 'Page permanently deleted.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete page: '.$e->getMessage());
        }
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        try {
            $this->pageService->emptyRecycleBin();

            return redirect()->route('page.recycleBin.index')
                ->with('success', 'Recycle bin emptied.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to empty recycle bin: '.$e->getMessage());
        }
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        try {
            $this->pageService->restoreAll();

            return redirect()->route('page.recycleBin.index')
                ->with('success', 'All pages restored.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to restore pages: '.$e->getMessage());
        }
    }
}
