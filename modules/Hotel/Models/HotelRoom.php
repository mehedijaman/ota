<?php

namespace Modules\Hotel\Models;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Hotel\Database\Factories\HotelRoomFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\Searchable;

class HotelRoom extends BaseModel
{
    use HasFactory, HasUuid, Searchable, SoftDeletes;

    protected $table = 'hotel_rooms';

    protected function casts(): array
    {
        return [
            'bed_count' => 'integer',
            'bed_configuration' => 'array',
            'max_adults' => 'integer',
            'max_children' => 'integer',
            'max_infants' => 'integer',
            'max_occupancy' => 'integer',
            'standard_occupancy' => 'integer',
            'room_size_sqm' => 'decimal:2',
            'room_size_sqft' => 'decimal:2',
            'is_smoking' => 'boolean',
            'is_accessible' => 'boolean',
            'has_balcony' => 'boolean',
            'has_terrace' => 'boolean',
            'has_kitchen' => 'boolean',
            'has_kitchenette' => 'boolean',
            'total_inventory' => 'integer',
            'images' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function newFactory(): HotelRoomFactory
    {
        return HotelRoomFactory::new();
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(HotelRoomType::class, 'room_type_id');
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(HotelRoomAmenity::class, 'hotel_room_amenity_room', 'room_id', 'room_amenity_id')
            ->withPivot('quantity', 'notes')
            ->withTimestamps();
    }

    public function rates(): HasMany
    {
        return $this->hasMany(HotelRate::class, 'room_id');
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(HotelInventory::class, 'room_id');
    }

    public function bookingRooms(): HasMany
    {
        return $this->hasMany(HotelBookingRoom::class, 'room_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getDisplayNameAttribute(): string
    {
        return $this->name.' - '.$this->roomType?->name;
    }

    public function getRoomSizeAttribute(): ?string
    {
        if ($this->room_size_sqm) {
            return $this->room_size_sqm.' m²';
        }
        if ($this->room_size_sqft) {
            return $this->room_size_sqft.' sq ft';
        }

        return null;
    }

    public function getOccupancyDisplayAttribute(): string
    {
        $parts = [];
        if ($this->max_adults) {
            $parts[] = $this->max_adults.' adult'.($this->max_adults > 1 ? 's' : '');
        }
        if ($this->max_children) {
            $parts[] = $this->max_children.' child'.($this->max_children > 1 ? 'ren' : '');
        }

        return implode(', ', $parts);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, int $roomTypeId)
    {
        return $query->where('room_type_id', $roomTypeId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeForHotel($query, int $hotelId)
    {
        return $query->where('hotel_id', $hotelId);
    }

    // Methods
    public function getAvailability(string $date): ?HotelInventory
    {
        return $this->inventories()->where('date', $date)->first();
    }

    public function getRate(int $ratePlanId, string $date): ?HotelRate
    {
        return $this->rates()
            ->where('rate_plan_id', $ratePlanId)
            ->where('date', $date)
            ->first();
    }

    public function isAvailable(string $date, int $quantity = 1): bool
    {
        $inventory = $this->getAvailability($date);
        if (! $inventory) {
            return false;
        }

        return $inventory->available >= $quantity;
    }
}
