<?php

namespace Modules\Hotel\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Hotel\Database\Factories\HotelChainFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class HotelChain extends BaseModel implements HasMedia
{
    use HasFactory, InteractsWithMedia, Searchable, SoftDeletes;

    protected $table = 'hotel_chains';

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'hotels_count' => 'integer',
        ];
    }

    protected static function newFactory(): HotelChainFactory
    {
        return HotelChainFactory::new();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
    }

    public function hotels(): HasMany
    {
        return $this->hasMany(Hotel::class, 'chain_id');
    }

    public function activeHotels(): HasMany
    {
        return $this->hotels()->where('is_active', true);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('logo') ?: null;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function updateHotelsCount(): void
    {
        $this->update(['hotels_count' => $this->hotels()->count()]);
    }
}
