<?php

namespace Modules\Slider\Services;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Modules\Slider\Models\Slider;

class SliderService
{
    /**
     * Get paginated list of all sliders.
     */
    public function getAll()
    {
        return Slider::query()
            ->orderBy('order')
            ->latest()
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($slider) => [
                'id' => $slider->id,
                'title' => $slider->title,
                'description' => $slider->description,
                'url' => $slider->url,
                'button_text' => $slider->button_text,
                'image_url' => $slider->image_url,
                'order' => $slider->order,
                'is_active' => $slider->is_active,
            ]);
    }

    /**
     * Get paginated list of trashed sliders.
     */
    public function getRecycleBin()
    {
        return Slider::onlyTrashed()
            ->orderBy('id')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($slider) => [
                'id' => $slider->id,
                'title' => $slider->title,
                'image_url' => $slider->image_url,
                'deleted_at' => $slider->deleted_at ? Carbon::parse($slider->deleted_at)->format('d/m/Y') : null,
                'deletedBy' => $slider->deletedBy,
                'is_active' => $slider->is_active,
            ]);
    }

    /**
     * Create a new slider with media handling.
     */
    public function create(array $data): Slider
    {
        return DB::transaction(function () use ($data) {
            $slider = Slider::create($this->prepareData($data));

            $this->handleMediaUpload($slider, $data);

            return $slider;
        });
    }

    /**
     * Update an existing slider with media handling.
     */
    public function update(Slider $slider, array $data): Slider
    {
        return DB::transaction(function () use ($slider, $data) {
            $slider->update($this->prepareData($data));

            $this->handleMediaUpdate($slider, $data);

            return $slider;
        });
    }

    /**
     * Soft delete a slider.
     */
    public function delete(Slider $slider): void
    {
        $slider->delete();
    }

    /**
     * Restore a soft-deleted slider.
     */
    public function restore(int $id): void
    {
        Slider::onlyTrashed()->findOrFail($id)->restore();
    }

    /**
     * Permanently delete a slider and its media.
     */
    public function forceDelete(int $id): void
    {
        $slider = Slider::onlyTrashed()->findOrFail($id);
        $slider->clearMediaCollection('image');
        $slider->forceDelete();
    }

    /**
     * Empty the recycle bin (permanently delete all trashed sliders).
     */
    public function emptyRecycleBin(): void
    {
        $sliders = Slider::onlyTrashed()->get();
        foreach ($sliders as $slider) {
            $slider->clearMediaCollection('image');
            $slider->forceDelete();
        }
    }

    /**
     * Restore all trashed sliders.
     */
    public function restoreAll(): void
    {
        Slider::onlyTrashed()->restore();
    }

    /**
     * Prepare data for creating or updating a slider.
     */
    protected function prepareData(array $data): array
    {
        return [
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'url' => $data['url'] ?? null,
            'button_text' => $data['button_text'] ?? null,
            'order' => $data['order'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ];
    }

    /**
     * Handle media upload for a new slider.
     */
    protected function handleMediaUpload(Slider $slider, array $data): void
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $slider->addMedia($data['image'])->toMediaCollection('image');
        }
    }

    /**
     * Handle media update for an existing slider.
     */
    protected function handleMediaUpdate(Slider $slider, array $data): void
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $slider->clearMediaCollection('image');
            $slider->addMedia($data['image'])->toMediaCollection('image');
        } elseif (! empty($data['remove_previous_image'])) {
            $slider->clearMediaCollection('image');
        }
    }
}
