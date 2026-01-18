<?php

namespace Modules\Flight\Models;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Flight\Database\Factories\FlightSupplierFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class FlightSupplier extends BaseModel implements HasMedia
{
    use HasFactory, HasUuid, InteractsWithMedia, Searchable, SoftDeletes;

    protected $table = 'flight_suppliers';

    protected function casts(): array
    {
        return [
            'api_credentials' => 'encrypted:array',
            'api_settings' => 'array',
            'is_active' => 'boolean',
            'is_production' => 'boolean',
            'priority' => 'integer',
            'timeout_seconds' => 'integer',
            'max_retries' => 'integer',
            'default_commission_rate' => 'decimal:2',
            'default_markup_rate' => 'decimal:2',
            'supported_features' => 'array',
            'supported_regions' => 'array',
            'excluded_airlines' => 'array',
        ];
    }

    protected static function newFactory(): FlightSupplierFactory
    {
        return FlightSupplierFactory::new();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
    }

    // Relationships
    public function flights(): HasMany
    {
        return $this->hasMany(Flight::class, 'supplier_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(FlightBooking::class, 'supplier_id');
    }

    public function syncLogs(): HasMany
    {
        return $this->hasMany(FlightSupplierSyncLog::class, 'supplier_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeProduction($query)
    {
        return $query->where('is_production', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'asc');
    }

    // Methods
    public function supportsFeature(string $feature): bool
    {
        return in_array($feature, $this->supported_features ?? []);
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('logo') ?: null;
    }
}
