<?php

namespace Modules\Tour\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Modules\Tour\Models\TourDestination;

class TourDestinationService
{
    public function getAll()
    {
        return TourDestination::query()
            ->withCount('tours')
            ->orderBy('name')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 15))
            ->withQueryString()
            ->through(fn ($dest) => [
                'id' => $dest->id,
                'name' => $dest->name,
                'slug' => $dest->slug,
                'country' => $dest->country,
                'city' => $dest->city,
                'image_url' => $dest->getFirstMediaUrl('image'),
                'tours_count' => $dest->tours_count,
                'is_featured' => $dest->is_featured,
                'is_active' => $dest->is_active,
            ]);
    }

    public function create(array $data): TourDestination
    {
        return DB::transaction(function () use ($data) {
            $destination = TourDestination::create($data);

            $this->handleMediaUploads($destination, $data);

            return $destination;
        });
    }

    public function update(TourDestination $destination, array $data): TourDestination
    {
        return DB::transaction(function () use ($destination, $data) {
            $destination->update($data);

            $this->handleMediaUpdates($destination, $data);

            return $destination;
        });
    }

    public function delete(TourDestination $destination): bool
    {
        if ($destination->tours()->exists()) {
            return false;
        }

        return $destination->delete();
    }

    public function getRecycleBin()
    {
        return TourDestination::onlyTrashed()
            ->withCount('tours')
            ->orderBy('deleted_at', 'desc')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 15))
            ->withQueryString()
            ->through(fn ($dest) => [
                'id' => $dest->id,
                'name' => $dest->name,
                'slug' => $dest->slug,
                'country' => $dest->country,
                'city' => $dest->city,
                'image_url' => $dest->getFirstMediaUrl('image'),
                'tours_count' => $dest->tours_count,
                'deleted_at' => $dest->deleted_at->toDateTimeString(),
            ]);
    }

    public function restore(int $id): bool
    {
        $destination = TourDestination::onlyTrashed()->findOrFail($id);

        return $destination->restore();
    }

    public function forceDelete(int $id): bool
    {
        $destination = TourDestination::onlyTrashed()->findOrFail($id);
        $destination->clearMediaCollection('image');
        $destination->clearMediaCollection('gallery');

        return $destination->forceDelete();
    }

    public function emptyRecycleBin(): void
    {
        TourDestination::onlyTrashed()->each(function ($destination) {
            $destination->clearMediaCollection('image');
            $destination->clearMediaCollection('gallery');
            $destination->forceDelete();
        });
    }

    public function restoreAll(): void
    {
        TourDestination::onlyTrashed()->restore();
    }

    protected function handleMediaUploads(TourDestination $destination, array $data): void
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $destination->addMedia($data['image'])->toMediaCollection('image');
        }

        if (isset($data['gallery']) && is_array($data['gallery'])) {
            foreach ($data['gallery'] as $image) {
                if ($image instanceof UploadedFile) {
                    $destination->addMedia($image)->toMediaCollection('gallery');
                }
            }
        }
    }

    protected function handleMediaUpdates(TourDestination $destination, array $data): void
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $destination->clearMediaCollection('image');
            $destination->addMedia($data['image'])->toMediaCollection('image');
        } elseif (! empty($data['remove_image'])) {
            $destination->clearMediaCollection('image');
        }

        if (isset($data['gallery']) && is_array($data['gallery'])) {
            foreach ($data['gallery'] as $image) {
                if ($image instanceof UploadedFile) {
                    $destination->addMedia($image)->toMediaCollection('gallery');
                }
            }
        }

        if (! empty($data['remove_gallery_ids']) && is_array($data['remove_gallery_ids'])) {
            foreach ($data['remove_gallery_ids'] as $mediaId) {
                $destination->media()->where('id', $mediaId)->delete();
            }
        }
    }
}
