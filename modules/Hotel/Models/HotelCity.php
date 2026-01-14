<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Hotel\Database\Factories\HotelCityFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\Searchable;

class HotelCity extends BaseModel
{
    use HasFactory, Searchable, SoftDeletes;

    protected $table = 'hotel_cities';

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
            'hotels_count' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    protected static function newFactory(): HotelCityFactory
    {
        return HotelCityFactory::new();
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(HotelCountry::class, 'country_id');
    }

    public function areas(): HasMany
    {
        return $this->hasMany(HotelArea::class, 'city_id');
    }

    public function activeAreas(): HasMany
    {
        return $this->areas()->where('is_active', true);
    }

    public function hotels(): HasMany
    {
        return $this->hasMany(Hotel::class, 'city_id');
    }

    public function getFullNameAttribute(): string
    {
        return $this->name.', '.$this->country?->name;
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

    public function scopeInCountry($query, int $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    public function updateHotelsCount(): void
    {
        $this->update(['hotels_count' => $this->hotels()->count()]);
    }
}
