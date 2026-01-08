<?php

namespace Modules\Testimonial\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Support\Http\Controllers\BackendController;
use Modules\Testimonial\Http\Requests\TestimonialValidate;
use Modules\Testimonial\Models\Testimonial;
use Modules\Testimonial\Services\TestimonialService;

class TestimonialController extends BackendController
{
    public function __construct(
        protected TestimonialService $testimonialService
    ) {}

    public function index(): Response
    {
        return inertia('Testimonial/TestimonialIndex', [
            'testimonials' => $this->testimonialService->getAll(),
        ]);
    }

    public function create(): Response
    {
        return inertia('Testimonial/TestimonialForm');
    }

    public function store(TestimonialValidate $request): RedirectResponse
    {
        try {
            $this->testimonialService->create($request->validated());

            return redirect()->route('testimonial.index')
                ->with('success', 'Testimonial created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create testimonial: '.$e->getMessage());
        }
    }

    public function edit(int $id): Response
    {
        $testimonial = Testimonial::findOrFail($id);

        return inertia('Testimonial/TestimonialForm', [
            'testimonial' => $testimonial,
        ]);
    }

    public function update(TestimonialValidate $request, int $id): RedirectResponse
    {
        try {
            $testimonial = Testimonial::findOrFail($id);
            $this->testimonialService->update($testimonial, $request->validated());

            return redirect()->route('testimonial.index')
                ->with('success', 'Testimonial updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update testimonial: '.$e->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $testimonial = Testimonial::findOrFail($id);
            $this->testimonialService->delete($testimonial);

            return redirect()->route('testimonial.index')
                ->with('success', 'Testimonial deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete testimonial: '.$e->getMessage());
        }
    }

    public function recycleBin(): Response
    {
        return inertia('Testimonial/TestimonialRecycleBin', [
            'testimonials' => $this->testimonialService->getRecycleBin(),
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        try {
            $this->testimonialService->restore($id);

            return redirect()->route('testimonial.recycleBin.index')
                ->with('success', 'Testimonial restored successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to restore testimonial: '.$e->getMessage());
        }
    }

    public function destroyForce(int $id): RedirectResponse
    {
        try {
            $this->testimonialService->forceDelete($id);

            return redirect()->route('testimonial.recycleBin.index')
                ->with('success', 'Testimonial permanently deleted.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete testimonial: '.$e->getMessage());
        }
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        try {
            $this->testimonialService->emptyRecycleBin();

            return redirect()->route('testimonial.recycleBin.index')
                ->with('success', 'Recycle bin emptied.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to empty recycle bin: '.$e->getMessage());
        }
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        try {
            $this->testimonialService->restoreAll();

            return redirect()->route('testimonial.recycleBin.index')
                ->with('success', 'All testimonials restored.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to restore testimonials: '.$e->getMessage());
        }
    }
}
