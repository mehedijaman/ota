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

    public static function getFeaturedTours($limit = 5)
    {
        return Tour::query()
        ->active()
        ->featured()
        ->with(['category', 'destination'])
        ->take($limit)
        ->get();
    }

    public function getById(int $id): array
    {
        $tour = Tour::with([
            'category',
            'destination',
            'itineraries',
            'inclusions',
            'exclusions',
            'highlights',
            'faqs',
            'pricings',
            'extras',
            'reviews' => fn ($q) => $q->approved()->latest()->limit(5),
        ])->findOrFail($id);

        return [
            'id' => $tour->id,
            'name' => $tour->name,
            'code' => $tour->code,
            'slug' => $tour->slug,
            'excerpt' => $tour->excerpt,
            'description' => $tour->description,
            'duration_days' => $tour->duration_days,
            'duration_nights' => $tour->duration_nights,
            'duration' => $tour->duration,
            'difficulty_level' => $tour->difficulty_level,
            'tour_type' => $tour->tour_type,
            'min_group_size' => $tour->min_group_size,
            'max_group_size' => $tour->max_group_size,
            'base_price' => $tour->base_price,
            'discount_price' => $tour->discount_price,
            'child_price' => $tour->child_price,
            'infant_price' => $tour->infant_price,
            'deposit_amount' => $tour->deposit_amount,
            'deposit_type' => $tour->deposit_type,
            'cancellation_days' => $tour->cancellation_days,
            'start_location' => $tour->start_location,
            'end_location' => $tour->end_location,
            'languages' => $tour->languages,
            'is_featured' => $tour->is_featured,
            'is_bestseller' => $tour->is_bestseller,
            'is_active' => $tour->is_active,
            'average_rating' => $tour->average_rating,
            'reviews_count' => $tour->reviews_count,
            'meta_title' => $tour->meta_title,
            'meta_description' => $tour->meta_description,
            'created_at' => $tour->created_at?->format('M d, Y H:i'),
            'updated_at' => $tour->updated_at?->format('M d, Y H:i'),
            'category' => $tour->category ? [
                'id' => $tour->category->id,
                'name' => $tour->category->name,
            ] : null,
            'destination' => $tour->destination ? [
                'id' => $tour->destination->id,
                'name' => $tour->destination->name,
            ] : null,
            'thumbnail_url' => $tour->getFirstMediaUrl('thumbnail'),
            'gallery' => $tour->getMedia('gallery')->map(fn ($m) => [
                'id' => $m->id,
                'url' => $m->getUrl(),
                'name' => $m->file_name,
            ])->toArray(),
            'itineraries' => $tour->itineraries->map(fn ($i) => [
                'day_number' => $i->day_number,
                'title' => $i->title,
                'description' => $i->description,
                'meals_included' => $i->meals_included,
                'accommodation' => $i->accommodation,
            ])->toArray(),
            'inclusions' => $tour->inclusions->pluck('title')->toArray(),
            'exclusions' => $tour->exclusions->pluck('title')->toArray(),
            'highlights' => $tour->highlights->pluck('title')->toArray(),
            'faqs' => $tour->faqs->map(fn ($f) => [
                'question' => $f->question,
                'answer' => $f->answer,
            ])->toArray(),
            'extras' => $tour->extras->map(fn ($e) => [
                'id' => $e->id,
                'name' => $e->name,
                'price' => $e->price,
            ])->toArray(),
            'reviews' => $tour->reviews->map(fn ($r) => [
                'rating' => $r->rating,
                'reviewer_name' => $r->reviewer_name,
                'comment' => $r->comment,
                'created_at' => $r->created_at->format('M d, Y'),
            ])->toArray(),
        ];
    }

    public function getForEdit(int $id): array
    {
        $tour = Tour::with(['pricings', 'itineraries', 'inclusions', 'exclusions'])->findOrFail($id);

        return [
            ...$tour->toArray(),
            'thumbnail_url' => $tour->getFirstMediaUrl('thumbnail'),
            'pricings' => $tour->pricings->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'description' => $p->description,
                'adult_price' => $p->adult_price,
                'child_price' => $p->child_price,
                'infant_price' => $p->infant_price,
            ])->toArray(),
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

            $this->syncPricings($tour, $data['pricings'] ?? []);
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

            $this->syncPricings($tour, $data['pricings'] ?? []);
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

        return $tour->delete();
    }

    public function getRecycleBin()
    {
        return Tour::query()
            ->onlyTrashed()
            ->with(['category', 'destination'])
            ->orderBy('deleted_at', 'desc')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 15))
            ->withQueryString()
            ->through(fn ($tour) => [
                'id' => $tour->id,
                'name' => $tour->name,
                'code' => $tour->code,
                'category' => $tour->category?->name,
                'destination' => $tour->destination?->name,
                'duration' => $tour->duration,
                'deleted_at' => $tour->deleted_at->format('M d, Y H:i'),
            ]);
    }

    public function restore(int $id): bool
    {
        $tour = Tour::onlyTrashed()->findOrFail($id);

        return $tour->restore();
    }

    public function forceDelete(int $id): bool
    {
        $tour = Tour::onlyTrashed()->findOrFail($id);

        return DB::transaction(function () use ($tour) {
            $tour->itineraries()->delete();
            $tour->allInclusions()->delete();
            $tour->highlights()->delete();
            $tour->faqs()->delete();
            $tour->clearMediaCollection('thumbnail');
            $tour->clearMediaCollection('gallery');

            return $tour->forceDelete();
        });
    }

    public function emptyRecycleBin(): void
    {
        $tours = Tour::onlyTrashed()->get();

        foreach ($tours as $tour) {
            $this->forceDelete($tour->id);
        }
    }

    public function restoreAll(): void
    {
        Tour::onlyTrashed()->restore();
    }

    protected function extractTourFields(array $data): array
    {
        $excluded = ['pricings', 'itineraries', 'inclusions', 'exclusions', 'thumbnail', 'gallery', 'remove_thumbnail', 'remove_gallery_ids'];

        return array_diff_key($data, array_flip($excluded));
    }

    protected function syncPricings(Tour $tour, array $pricings): void
    {
        $tour->pricings()->delete();

        foreach ($pricings as $pricing) {
            if (! empty($pricing['name'])) {
                $tour->pricings()->create([
                    'name' => $pricing['name'],
                    'description' => $pricing['description'] ?? null,
                    'adult_price' => $pricing['adult_price'] ?? 0,
                    'child_price' => $pricing['child_price'] ?? null,
                    'infant_price' => $pricing['infant_price'] ?? null,
                ]);
            }
        }
    }

    protected function syncItineraries(Tour $tour, array $itineraries): void
    {
        $tour->itineraries()->delete();

        foreach ($itineraries as $itin) {
            if (! empty($itin['title'])) {
                TourItinerary::create([
                    'tour_id' => $tour->id,
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
