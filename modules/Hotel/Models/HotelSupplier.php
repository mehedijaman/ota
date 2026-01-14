<?php

namespace Modules\Hotel\Models;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Hotel\Database\Factories\HotelSupplierFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\Searchable;

class HotelSupplier extends BaseModel
{
    use HasFactory, HasUuid, Searchable, SoftDeletes;

    protected $table = 'hotel_suppliers';

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'priority' => 'integer',
            'commission_rate' => 'decimal:2',
            'markup_rate' => 'decimal:2',
            'timeout_seconds' => 'integer',
            'retry_attempts' => 'integer',
            'settings' => 'array',
            'supported_currencies' => 'array',
            'supported_languages' => 'array',
        ];
    }

    protected static function newFactory(): HotelSupplierFactory
    {
        return HotelSupplierFactory::new();
    }

    public function credentials(): HasMany
    {
        return $this->hasMany(HotelSupplierCredential::class, 'supplier_id');
    }

    public function activeCredentials(): HasMany
    {
        return $this->credentials()->where('is_active', true);
    }

    public function productionCredential()
    {
        return $this->credentials()->where('environment', 'production')->where('is_active', true)->first();
    }

    public function sandboxCredential()
    {
        return $this->credentials()->where('environment', 'sandbox')->where('is_active', true)->first();
    }

    public function hotels(): HasMany
    {
        return $this->hasMany(Hotel::class, 'supplier_id');
    }

    public function syncLogs(): HasMany
    {
        return $this->hasMany(HotelSupplierSyncLog::class, 'supplier_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByPriority($query)
    {
        return $query->orderBy('priority');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
