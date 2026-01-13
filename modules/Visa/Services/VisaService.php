<?php

namespace Modules\Visa\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Modules\Visa\Models\Visa;

class VisaService
{
    public function getAll(): LengthAwarePaginator
    {
        return Visa::query()
            ->with(['country', 'category'])
            ->when(request('country_id'), fn ($q, $countryId) => $q->where('country_id', $countryId))
            ->when(request('category_id'), fn ($q, $categoryId) => $q->where('category_id', $categoryId))
            ->when(request('status') !== null && request('status') !== '', function ($q) {
                $q->where('is_active', request('status') === 'active');
            })
            ->when(request('is_featured') !== null && request('is_featured') !== '', function ($q) {
                $q->where('is_featured', request('is_featured') === 'yes');
            })
            ->when(request('is_popular') !== null && request('is_popular') !== '', function ($q) {
                $q->where('is_popular', request('is_popular') === 'yes');
            })
            ->when(request('entry_type'), fn ($q, $type) => $q->where('entry_type', $type))
            ->orderBy('id', 'desc')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 15))
            ->withQueryString()
            ->through(fn (Visa $visa) => [
                'id' => $visa->id,
                'name' => $visa->name,
                'code' => $visa->code,
                'slug' => $visa->slug,
                'thumbnail_url' => $visa->getFirstMediaUrl('thumbnail'),
                'country' => $visa->country?->name,
                'category' => $visa->category?->name,
                'entry_type' => $visa->entry_type,
                'validity_days' => $visa->validity_days,
                'stay_days' => $visa->stay_days,
                'processing_days' => $visa->processing_days_min.'-'.$visa->processing_days_max,
                'base_price' => $visa->base_price,
                'service_fee' => $visa->service_fee,
                'total_price' => $visa->total_price,
                'success_rate' => $visa->success_rate,
                'is_featured' => $visa->is_featured,
                'is_popular' => $visa->is_popular,
                'is_active' => $visa->is_active,
            ]);
    }

    /**
     * @return array<string, int>
     */
    public function getStats(): array
    {
        return [
            'total' => Visa::count(),
            'active' => Visa::where('is_active', true)->count(),
            'inactive' => Visa::where('is_active', false)->count(),
            'featured' => Visa::where('is_featured', true)->count(),
            'popular' => Visa::where('is_popular', true)->count(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getById(int $id): array
    {
        $visa = Visa::with([
            'country',
            'category',
            'requirements' => fn ($q) => $q->orderBy('sort_order'),
            'fees' => fn ($q) => $q->active()->orderBy('sort_order'),
            'processingTimes' => fn ($q) => $q->active()->orderBy('sort_order'),
            'faqs' => fn ($q) => $q->active()->orderBy('sort_order'),
        ])->findOrFail($id);

        return [
            'id' => $visa->id,
            'name' => $visa->name,
            'code' => $visa->code,
            'slug' => $visa->slug,
            'country_id' => $visa->country_id,
            'category_id' => $visa->category_id,
            'country' => $visa->country,
            'category' => $visa->category,
            'excerpt' => $visa->excerpt,
            'description' => $visa->description,
            'eligibility_criteria' => $visa->eligibility_criteria,
            'terms_conditions' => $visa->terms_conditions,
            'important_notes' => $visa->important_notes,
            'entry_type' => $visa->entry_type,
            'validity_days' => $visa->validity_days,
            'stay_days' => $visa->stay_days,
            'processing_days_min' => $visa->processing_days_min,
            'processing_days_max' => $visa->processing_days_max,
            'base_price' => $visa->base_price,
            'service_fee' => $visa->service_fee,
            'embassy_fee' => $visa->embassy_fee,
            'vfs_fee' => $visa->vfs_fee,
            'total_price' => $visa->total_price,
            'success_rate' => $visa->success_rate,
            'is_express_available' => $visa->is_express_available,
            'is_interview_required' => $visa->is_interview_required,
            'is_biometric_required' => $visa->is_biometric_required,
            'is_appointment_required' => $visa->is_appointment_required,
            'is_travel_insurance_required' => $visa->is_travel_insurance_required,
            'is_bank_statement_required' => $visa->is_bank_statement_required,
            'is_invitation_letter_required' => $visa->is_invitation_letter_required,
            'is_hotel_booking_required' => $visa->is_hotel_booking_required,
            'is_flight_booking_required' => $visa->is_flight_booking_required,
            'included_services' => $visa->included_services,
            'excluded_services' => $visa->excluded_services,
            'is_featured' => $visa->is_featured,
            'is_popular' => $visa->is_popular,
            'is_active' => $visa->is_active,
            'valid_from' => $visa->valid_from?->format('Y-m-d'),
            'valid_to' => $visa->valid_to?->format('Y-m-d'),
            'meta_title' => $visa->meta_title,
            'meta_description' => $visa->meta_description,
            'meta_keywords' => $visa->meta_keywords,
            'thumbnail_url' => $visa->getFirstMediaUrl('thumbnail'),
            'banner_url' => $visa->getFirstMediaUrl('banner'),
            'gallery' => $visa->getMedia('gallery')->map(fn ($media) => [
                'id' => $media->id,
                'url' => $media->getUrl(),
            ]),
            'requirements' => $visa->requirements,
            'fees' => $visa->fees,
            'processingTimes' => $visa->processingTimes,
            'faqs' => $visa->faqs,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getForEdit(int $id): array
    {
        return $this->getById($id);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Visa
    {
        return DB::transaction(function () use ($data) {
            $visa = Visa::create($data);

            $this->syncRelatedData($visa, $data);
            $this->handleMediaUploads($visa, $data);

            return $visa;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Visa $visa, array $data): Visa
    {
        return DB::transaction(function () use ($visa, $data) {
            $visa->update($data);

            $this->syncRelatedData($visa, $data);
            $this->handleMediaUploads($visa, $data);

            return $visa;
        });
    }

    public function delete(Visa $visa): bool
    {
        if ($visa->applications()->exists()) {
            return false;
        }

        $visa->delete();

        return true;
    }

    public function restore(int $id): void
    {
        Visa::withTrashed()->findOrFail($id)->restore();
    }

    public function forceDelete(int $id): void
    {
        $visa = Visa::withTrashed()->findOrFail($id);
        $visa->requirements()->delete();
        $visa->fees()->delete();
        $visa->processingTimes()->delete();
        $visa->faqs()->delete();
        $visa->clearMediaCollection('thumbnail');
        $visa->clearMediaCollection('gallery');
        $visa->forceDelete();
    }

    public function getRecycleBin(): LengthAwarePaginator
    {
        return Visa::onlyTrashed()
            ->with(['country', 'category'])
            ->orderBy('deleted_at', 'desc')
            ->paginate(request('rowsPerPage', 15))
            ->withQueryString()
            ->through(fn (Visa $visa) => [
                'id' => $visa->id,
                'name' => $visa->name,
                'code' => $visa->code,
                'country' => $visa->country?->name,
                'category' => $visa->category?->name,
                'deleted_at' => $visa->deleted_at->format('M d, Y'),
            ]);
    }

    public function emptyRecycleBin(): void
    {
        Visa::onlyTrashed()->each(function (Visa $visa) {
            $visa->clearMediaCollection('thumbnail');
            $visa->clearMediaCollection('gallery');
            $visa->forceDelete();
        });
    }

    public function restoreAll(): void
    {
        Visa::onlyTrashed()->restore();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function syncRelatedData(Visa $visa, array $data): void
    {
        // Sync Requirements
        if (isset($data['requirements'])) {
            $visa->requirements()->delete();
            foreach ($data['requirements'] as $index => $requirement) {
                $visa->requirements()->create([
                    ...$requirement,
                    'sort_order' => $index + 1,
                ]);
            }
        }

        // Sync Fees
        if (isset($data['fees'])) {
            $visa->fees()->delete();
            foreach ($data['fees'] as $index => $fee) {
                $visa->fees()->create([
                    ...$fee,
                    'sort_order' => $index + 1,
                ]);
            }
        }

        // Sync Processing Times
        if (isset($data['processing_times'])) {
            $visa->processingTimes()->delete();
            foreach ($data['processing_times'] as $index => $processingTime) {
                $visa->processingTimes()->create([
                    ...$processingTime,
                    'sort_order' => $index + 1,
                ]);
            }
        }

        // Sync FAQs
        if (isset($data['faqs'])) {
            $visa->faqs()->delete();
            foreach ($data['faqs'] as $index => $faq) {
                $visa->faqs()->create([
                    ...$faq,
                    'sort_order' => $index + 1,
                ]);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleMediaUploads(Visa $visa, array $data): void
    {
        if (! empty($data['remove_thumbnail'])) {
            $visa->clearMediaCollection('thumbnail');
        }

        if (isset($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {
            $visa->clearMediaCollection('thumbnail');
            $visa->addMedia($data['thumbnail'])->toMediaCollection('thumbnail');
        }

        if (! empty($data['remove_banner'])) {
            $visa->clearMediaCollection('banner');
        }

        if (isset($data['banner']) && $data['banner'] instanceof UploadedFile) {
            $visa->clearMediaCollection('banner');
            $visa->addMedia($data['banner'])->toMediaCollection('banner');
        }

        if (isset($data['gallery']) && is_array($data['gallery'])) {
            foreach ($data['gallery'] as $image) {
                if ($image instanceof UploadedFile) {
                    $visa->addMedia($image)->toMediaCollection('gallery');
                }
            }
        }

        if (isset($data['remove_gallery_images']) && is_array($data['remove_gallery_images'])) {
            foreach ($data['remove_gallery_images'] as $mediaId) {
                $visa->getMedia('gallery')->where('id', $mediaId)->first()?->delete();
            }
        }
    }
}
