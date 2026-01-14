<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Hotel\Database\Factories\HotelRoomTypeFactory;
use Modules\Support\Models\BaseModel;

class HotelRoomType extends BaseModel
{
    use HasFactory;

    protected $table = 'hotel_room_types';

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function newFactory(): HotelRoomTypeFactory
    {
        return HotelRoomTypeFactory::new();
    }

    public function rooms()
    {
        return $this->hasMany(HotelRoom::class, 'room_type_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
