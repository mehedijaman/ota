<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Support\Models\BaseModel;

class HotelRoomAmenity extends BaseModel
{
    use HasFactory;

    protected $table = 'hotel_room_amenities';

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(HotelRoom::class, 'hotel_room_amenity_room', 'room_amenity_id', 'room_id')
            ->withPivot('quantity', 'notes')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
