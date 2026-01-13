<?php

namespace Modules\Visa\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Modules\Visa\Models\VisaCountry;

class VisaCountryService
{
    public function getAll(): LengthAwarePaginator
    {
        return VisaCountry::query()
            ->withCount('visas')
            ->when(request('continent'), fn ($q, $continent) => $q->where('continent', $continent))
            ->when(request('status') !== null && request('status') !== '', function ($q) {
                $q->where('is_active', request('status') === 'active');
            })
            ->when(request('is_popular') !== null && request('is_popular') !== '', function ($q) {
                $q->where('is_popular', request('is_popular') === 'yes');
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 15))
            ->withQueryString()
            ->through(fn (VisaCountry $country) => [
                'id' => $country->id,
                'name' => $country->name,
                'code' => $country->code,
                'slug' => $country->slug,
                'continent' => $country->continent,
                'flag_url' => $country->getFirstMediaUrl('flag'),
                'visas_count' => $country->visas_count,
                'is_popular' => $country->is_popular,
                'is_active' => $country->is_active,
                'sort_order' => $country->sort_order,
            ]);
    }

    /**
     * @return array<string, int>
     */
    public function getStats(): array
    {
        return [
            'total' => VisaCountry::count(),
            'active' => VisaCountry::where('is_active', true)->count(),
            'inactive' => VisaCountry::where('is_active', false)->count(),
            'popular' => VisaCountry::where('is_popular', true)->count(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getById(int $id): array
    {
        $country = VisaCountry::with(['visas' => fn ($q) => $q->active()->limit(10)])
            ->withCount('visas')
            ->findOrFail($id);

        return [
            'id' => $country->id,
            'name' => $country->name,
            'code' => $country->code,
            'slug' => $country->slug,
            'continent' => $country->continent,
            'capital' => $country->capital,
            'timezone' => $country->timezone,
            'description' => $country->description,
            'visa_info' => $country->visa_info,
            'embassy_name' => $country->embassy_name,
            'embassy_address' => $country->embassy_address,
            'embassy_phone' => $country->embassy_phone,
            'embassy_email' => $country->embassy_email,
            'embassy_website' => $country->embassy_website,
            'embassy_working_hours' => $country->embassy_working_hours,
            'is_popular' => $country->is_popular,
            'is_active' => $country->is_active,
            'sort_order' => $country->sort_order,
            'meta_title' => $country->meta_title,
            'meta_description' => $country->meta_description,
            'meta_keywords' => $country->meta_keywords,
            'flag_url' => $country->getFirstMediaUrl('flag'),
            'banner_url' => $country->getFirstMediaUrl('banner'),
            'visas_count' => $country->visas_count,
            'visas' => $country->visas,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getForEdit(int $id): array
    {
        $country = VisaCountry::findOrFail($id);

        return [
            'id' => $country->id,
            'name' => $country->name,
            'code' => $country->code,
            'slug' => $country->slug,
            'continent' => $country->continent,
            'capital' => $country->capital,
            'timezone' => $country->timezone,
            'description' => $country->description,
            'visa_info' => $country->visa_info,
            'embassy_name' => $country->embassy_name,
            'embassy_address' => $country->embassy_address,
            'embassy_phone' => $country->embassy_phone,
            'embassy_email' => $country->embassy_email,
            'embassy_website' => $country->embassy_website,
            'embassy_working_hours' => $country->embassy_working_hours,
            'is_popular' => $country->is_popular,
            'is_active' => $country->is_active,
            'sort_order' => $country->sort_order,
            'meta_title' => $country->meta_title,
            'meta_description' => $country->meta_description,
            'meta_keywords' => $country->meta_keywords,
            'flag_url' => $country->getFirstMediaUrl('flag'),
            'banner_url' => $country->getFirstMediaUrl('banner'),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): VisaCountry
    {
        $country = VisaCountry::create($data);

        $this->handleMediaUploads($country, $data);

        return $country;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(VisaCountry $country, array $data): VisaCountry
    {
        $country->update($data);

        $this->handleMediaUploads($country, $data);

        return $country;
    }

    public function delete(VisaCountry $country): bool
    {
        if ($country->visas()->exists()) {
            return false;
        }

        $country->delete();

        return true;
    }

    public function restore(int $id): void
    {
        VisaCountry::withTrashed()->findOrFail($id)->restore();
    }

    public function forceDelete(int $id): void
    {
        $country = VisaCountry::withTrashed()->findOrFail($id);
        $country->clearMediaCollection('flag');
        $country->clearMediaCollection('banner');
        $country->forceDelete();
    }

    public function getRecycleBin(): LengthAwarePaginator
    {
        return VisaCountry::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(request('rowsPerPage', 15))
            ->withQueryString()
            ->through(fn (VisaCountry $country) => [
                'id' => $country->id,
                'name' => $country->name,
                'code' => $country->code,
                'deleted_at' => $country->deleted_at->format('M d, Y'),
            ]);
    }

    public function emptyRecycleBin(): void
    {
        VisaCountry::onlyTrashed()->each(function (VisaCountry $country) {
            $country->clearMediaCollection('flag');
            $country->clearMediaCollection('banner');
            $country->forceDelete();
        });
    }

    public function restoreAll(): void
    {
        VisaCountry::onlyTrashed()->restore();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleMediaUploads(VisaCountry $country, array $data): void
    {
        if (! empty($data['remove_flag'])) {
            $country->clearMediaCollection('flag');
        }

        if (! empty($data['remove_banner'])) {
            $country->clearMediaCollection('banner');
        }

        if (isset($data['flag']) && $data['flag'] instanceof UploadedFile) {
            $country->clearMediaCollection('flag');
            $country->addMedia($data['flag'])->toMediaCollection('flag');
        }

        if (isset($data['banner']) && $data['banner'] instanceof UploadedFile) {
            $country->clearMediaCollection('banner');
            $country->addMedia($data['banner'])->toMediaCollection('banner');
        }
    }
}
