<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\Searchable;

class HotelArea extends BaseModel
{
    use HasFactory, Searchable;

    protected $table = 'hotel_areas';

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'radius_km' => 'decimal:2',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
            'hotels_count' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(HotelCity::class, 'city_id');
    }

    public function hotels(): HasMany
    {
        return $this->hasMany(Hotel::class, 'area_id');
    }

    public function getFullNameAttribute(): string
    {
        return $this->name.', '.$this->city?->name;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeInCity($query, int $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    public function updateHotelsCount(): void
    {
        $this->update(['hotels_count' => $this->hotels()->count()]);
    }
}
