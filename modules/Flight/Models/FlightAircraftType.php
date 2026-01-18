<?php

namespace Modules\Flight\Models;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Flight\Database\Factories\FlightAircraftTypeFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class FlightAircraftType extends BaseModel implements HasMedia
{
    use HasFactory, HasUuid, InteractsWithMedia, Searchable;

    protected $table = 'flight_aircraft_types';

    protected function casts(): array
    {
        return [
            'engine_count' => 'integer',
            'typical_seats' => 'integer',
            'max_seats' => 'integer',
            'first_class_seats' => 'integer',
            'business_class_seats' => 'integer',
            'economy_class_seats' => 'integer',
            'range_km' => 'integer',
            'cruise_speed_kmh' => 'integer',
            'max_altitude_ft' => 'integer',
            'wingspan_m' => 'decimal:2',
            'length_m' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    protected static function newFactory(): FlightAircraftTypeFactory
    {
        return FlightAircraftTypeFactory::new();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
        $this->addMediaCollection('seat_map')->singleFile();
    }

    // Relationships
    public function flights(): HasMany
    {
        return $this->hasMany(Flight::class, 'aircraft_type_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Accessors
    public function getImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('image') ?: $this->image;
    }

    public function getSeatMapUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('seat_map') ?: $this->seat_map_image;
    }

    public function getDisplayNameAttribute(): string
    {
        return "{$this->manufacturer} {$this->model}".($this->variant ? " {$this->variant}" : '');
    }

    public function getTotalCapacityAttribute(): int
    {
        return ($this->first_class_seats ?? 0)
            + ($this->business_class_seats ?? 0)
            + ($this->economy_class_seats ?? 0);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByManufacturer($query, string $manufacturer)
    {
        return $query->where('manufacturer', $manufacturer);
    }

    public function scopeWideBody($query)
    {
        return $query->where('category', 'wide_body');
    }

    public function scopeNarrowBody($query)
    {
        return $query->where('category', 'narrow_body');
    }

    // Methods
    public function isWideBody(): bool
    {
        return $this->category === 'wide_body';
    }

    public function isJet(): bool
    {
        return $this->engine_type === 'jet';
    }
}
