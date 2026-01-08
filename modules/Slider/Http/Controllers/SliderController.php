<?php

namespace Modules\Slider\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Slider\Http\Requests\SliderValidate;
use Modules\Slider\Models\Slider;
use Modules\Slider\Services\SliderService;
use Modules\Support\Http\Controllers\BackendController;

class SliderController extends BackendController
{
    public function __construct(
        protected SliderService $sliderService
    ) {}

    public function index(): Response
    {
        return inertia('Slider/SliderIndex', [
            'sliders' => $this->sliderService->getAll(),
        ]);
    }

    public function create(): Response
    {
        return inertia('Slider/SliderForm');
    }

    public function store(SliderValidate $request): RedirectResponse
    {
        try {
            $this->sliderService->create($request->validated());

            return redirect()->route('slider.index')
                ->with('success', 'Slider created.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function edit(int $id): Response
    {
        $slider = Slider::findOrFail($id);

        return inertia('Slider/SliderForm', [
            'slider' => $slider,
        ]);
    }

    public function update(SliderValidate $request, int $id): RedirectResponse
    {
        try {
            $slider = Slider::findOrFail($id);
            $this->sliderService->update($slider, $request->validated());

            return redirect()->route('slider.index')
                ->with('success', 'Slider updated.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $slider = Slider::findOrFail($id);
            $this->sliderService->delete($slider);

            return redirect()->route('slider.index')
                ->with('success', 'Slider deleted.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function recycleBin(): Response
    {
        return inertia('Slider/SliderRecycleBin', [
            'sliders' => $this->sliderService->getRecycleBin(),
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        try {
            $this->sliderService->restore($id);

            return redirect()->route('slider.recycleBin.index')
                ->with('success', 'Slider restored.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroyForce(int $id): RedirectResponse
    {
        try {
            $this->sliderService->forceDelete($id);

            return redirect()->route('slider.recycleBin.index')
                ->with('success', 'Slider permanently deleted.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        try {
            $this->sliderService->emptyRecycleBin();

            return redirect()->route('slider.recycleBin.index')
                ->with('success', 'Recycle bin emptied.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        try {
            $this->sliderService->restoreAll();

            return redirect()->route('slider.recycleBin.index')
                ->with('success', 'All sliders restored.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
}
