<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Hotel\Database\Factories\HotelCountryFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\Searchable;

class HotelCountry extends BaseModel
{
    use HasFactory, Searchable, SoftDeletes;

    protected $table = 'hotel_countries';

    protected function casts(): array
    {
        return [
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
            'hotels_count' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    protected static function newFactory(): HotelCountryFactory
    {
        return HotelCountryFactory::new();
    }

    public function cities(): HasMany
    {
        return $this->hasMany(HotelCity::class, 'country_id');
    }

    public function activeCities(): HasMany
    {
        return $this->cities()->where('is_active', true);
    }

    public function hotels(): HasMany
    {
        return $this->hasMany(Hotel::class, 'country_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function updateHotelsCount(): void
    {
        $this->update(['hotels_count' => $this->hotels()->count()]);
    }
}
