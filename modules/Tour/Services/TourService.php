<?php

namespace Modules\Tour\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Modules\Tour\Models\Tour;
use Modules\Tour\Models\TourInclusion;
use Modules\Tour\Models\TourItinerary;

class TourService
{
    public function getAll()
    {
        return Tour::query()
            ->with(['category', 'destination'])
            ->orderBy('id', 'desc')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 15))
            ->withQueryString()
            ->through(fn ($tour) => [
                'id' => $tour->id,
                'name' => $tour->name,
                'code' => $tour->code,
                'slug' => $tour->slug,
                'thumbnail_url' => $tour->getFirstMediaUrl('thumbnail'),
                'category' => $tour->category?->name,
                'destination' => $tour->destination?->name,
                'duration' => $tour->duration,
                'base_price' => $tour->base_price,
                'is_featured' => $tour->is_featured,
                'is_bestseller' => $tour->is_bestseller,
                'is_active' => $tour->is_active,
            ]);
    }

    public function getById(int $id): Tour
    {
        return Tour::with([
            'category',
            'destination',
            'itineraries',
            'inclusions',
            'exclusions',
            'highlights',
            'pricings',
            'departureDates' => fn ($q) => $q->upcoming()->limit(10),
        ])->findOrFail($id);
    }

    public function getForEdit(int $id): array
    {
        $tour = Tour::with(['itineraries', 'inclusions', 'exclusions'])->findOrFail($id);

        return [
            ...$tour->toArray(),
            'thumbnail_url' => $tour->getFirstMediaUrl('thumbnail'),
            'inclusions' => $tour->inclusions->pluck('title')->toArray(),
            'exclusions' => $tour->exclusions->pluck('title')->toArray(),
            'gallery' => $tour->getMedia('gallery')->map(fn ($m) => [
                'id' => $m->id,
                'url' => $m->getUrl(),
                'name' => $m->file_name,
            ])->toArray(),
        ];
    }

    public function create(array $data): Tour
    {
        return DB::transaction(function () use ($data) {
            $tourData = $this->extractTourFields($data);
            $tour = Tour::create($tourData);

            $this->syncItineraries($tour, $data['itineraries'] ?? []);
            $this->syncInclusions($tour, $data['inclusions'] ?? [], 'inclusion');
            $this->syncInclusions($tour, $data['exclusions'] ?? [], 'exclusion');
            $this->handleMediaUploads($tour, $data);

            return $tour;
        });
    }

    public function update(Tour $tour, array $data): Tour
    {
        return DB::transaction(function () use ($tour, $data) {
            $tourData = $this->extractTourFields($data);
            $tour->update($tourData);

            $this->syncItineraries($tour, $data['itineraries'] ?? []);
            $this->syncInclusions($tour, $data['inclusions'] ?? [], 'inclusion');
            $this->syncInclusions($tour, $data['exclusions'] ?? [], 'exclusion');
            $this->handleMediaUpdates($tour, $data);

            return $tour;
        });
    }

    public function delete(Tour $tour): bool
    {
        if ($tour->bookings()->exists()) {
            return false;
        }

        return DB::transaction(function () use ($tour) {
            $tour->itineraries()->delete();
            $tour->allInclusions()->delete();
            $tour->highlights()->delete();
            $tour->faqs()->delete();
            $tour->clearMediaCollection('thumbnail');
            $tour->clearMediaCollection('gallery');

            return $tour->delete();
        });
    }

    protected function extractTourFields(array $data): array
    {
        $excluded = ['itineraries', 'inclusions', 'exclusions', 'thumbnail', 'gallery', 'remove_thumbnail', 'remove_gallery_ids'];

        return array_diff_key($data, array_flip($excluded));
    }

    protected function syncItineraries(Tour $tour, array $itineraries): void
    {
        $tour->itineraries()->delete();

        foreach ($itineraries as $itin) {
            if (! empty($itin['title'])) {
                TourItinerary::create([
                    'tour_id' => $tour->id,
                    'day_number' => $itin['day_number'] ?? 1,
                    'title' => $itin['title'],
                    'description' => $itin['description'] ?? null,
                    'meals_included' => $itin['meals_included'] ?? [],
                    'accommodation' => $itin['accommodation'] ?? null,
                    'distance_km' => $itin['distance_km'] ?? null,
                    'activities' => $itin['activities'] ?? [],
                    'highlights' => $itin['highlights'] ?? [],
                ]);
            }
        }
    }

    protected function syncInclusions(Tour $tour, array $items, string $type): void
    {
        $tour->allInclusions()->where('type', $type)->delete();

        foreach ($items as $i => $title) {
            if (! empty($title)) {
                TourInclusion::create([
                    'tour_id' => $tour->id,
                    'type' => $type,
                    'title' => $title,
                    'sort_order' => $i,
                ]);
            }
        }
    }

    protected function handleMediaUploads(Tour $tour, array $data): void
    {
        if (isset($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {
            $tour->addMedia($data['thumbnail'])->toMediaCollection('thumbnail');
        }

        if (isset($data['gallery']) && is_array($data['gallery'])) {
            foreach ($data['gallery'] as $image) {
                if ($image instanceof UploadedFile) {
                    $tour->addMedia($image)->toMediaCollection('gallery');
                }
            }
        }
    }

    protected function handleMediaUpdates(Tour $tour, array $data): void
    {
        if (isset($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {
            $tour->clearMediaCollection('thumbnail');
            $tour->addMedia($data['thumbnail'])->toMediaCollection('thumbnail');
        } elseif (! empty($data['remove_thumbnail'])) {
            $tour->clearMediaCollection('thumbnail');
        }

        if (isset($data['gallery']) && is_array($data['gallery'])) {
            foreach ($data['gallery'] as $image) {
                if ($image instanceof UploadedFile) {
                    $tour->addMedia($image)->toMediaCollection('gallery');
                }
            }
        }

        if (! empty($data['remove_gallery_ids']) && is_array($data['remove_gallery_ids'])) {
            foreach ($data['remove_gallery_ids'] as $mediaId) {
                $tour->media()->where('id', $mediaId)->delete();
            }
        }
    }
}
