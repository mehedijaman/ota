<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Support\Models\BaseModel;

class HotelInventory extends BaseModel
{
    use HasFactory;

    protected $table = 'hotel_inventories';

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'total_inventory' => 'integer',
            'booked' => 'integer',
            'blocked' => 'integer',
            'held' => 'integer',
            'available' => 'integer',
            'oversell_limit' => 'integer',
            'last_sync_at' => 'datetime',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(HotelRoom::class, 'room_id');
    }

    // Scopes
    public function scopeForRoom($query, int $roomId)
    {
        return $query->where('room_id', $roomId);
    }

    public function scopeForDate($query, string $date)
    {
        return $query->where('date', $date);
    }

    public function scopeForDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeWithAvailability($query, int $minAvailable = 1)
    {
        return $query->where('available', '>=', $minAvailable);
    }

    // Methods
    public function recalculateAvailable(): void
    {
        $available = $this->total_inventory - $this->booked - $this->blocked - $this->held + $this->oversell_limit;
        $this->update(['available' => max(0, $available)]);
    }

    public function book(int $quantity = 1): bool
    {
        if ($this->available < $quantity) {
            return false;
        }

        $this->increment('booked', $quantity);
        $this->decrement('available', $quantity);

        return true;
    }

    public function release(int $quantity = 1): void
    {
        $this->decrement('booked', min($quantity, $this->booked));
        $this->recalculateAvailable();
    }

    public function hold(int $quantity = 1): bool
    {
        if ($this->available < $quantity) {
            return false;
        }

        $this->increment('held', $quantity);
        $this->decrement('available', $quantity);

        return true;
    }

    public function releaseHold(int $quantity = 1): void
    {
        $this->decrement('held', min($quantity, $this->held));
        $this->recalculateAvailable();
    }

    public function block(int $quantity = 1): void
    {
        $this->increment('blocked', $quantity);
        $this->recalculateAvailable();
    }

    public function unblock(int $quantity = 1): void
    {
        $this->decrement('blocked', min($quantity, $this->blocked));
        $this->recalculateAvailable();
    }

    public function isAvailable(int $quantity = 1): bool
    {
        return $this->available >= $quantity;
    }

    public function getEffectiveAvailableAttribute(): int
    {
        return max(0, $this->total_inventory - $this->booked - $this->blocked - $this->held + $this->oversell_limit);
    }
}
