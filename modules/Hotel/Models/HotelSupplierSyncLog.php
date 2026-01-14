<?php

namespace Modules\Hotel\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Support\Models\BaseModel;

class HotelSupplierSyncLog extends BaseModel
{
    use HasFactory;

    protected $table = 'hotel_supplier_sync_logs';

    protected function casts(): array
    {
        return [
            'request_data' => 'array',
            'response_data' => 'array',
            'request_headers' => 'array',
            'http_status_code' => 'integer',
            'hotels_synced' => 'integer',
            'rooms_synced' => 'integer',
            'rates_synced' => 'integer',
            'records_created' => 'integer',
            'records_updated' => 'integer',
            'records_failed' => 'integer',
            'duration_ms' => 'integer',
            'total_duration_ms' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(HotelSupplier::class, 'supplier_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(HotelBooking::class, 'booking_id');
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    public function triggeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }

    // Accessors
    public function getIsSuccessAttribute(): bool
    {
        return $this->status === 'success';
    }

    public function getIsFailedAttribute(): bool
    {
        return $this->status === 'failed';
    }

    public function getDurationSecondsAttribute(): ?float
    {
        return $this->duration_ms ? round($this->duration_ms / 1000, 2) : null;
    }

    public function getTotalRecordsSyncedAttribute(): int
    {
        return $this->records_created + $this->records_updated;
    }

    public function getSyncTypeLabelAttribute(): string
    {
        return match ($this->sync_type) {
            'availability' => 'Availability Sync',
            'rates' => 'Rates Sync',
            'inventory' => 'Inventory Sync',
            'hotel_content' => 'Hotel Content',
            'room_content' => 'Room Content',
            'booking_create' => 'Create Booking',
            'booking_confirm' => 'Confirm Booking',
            'booking_cancel' => 'Cancel Booking',
            'booking_modify' => 'Modify Booking',
            'search' => 'Search',
            'full_sync' => 'Full Sync',
            default => ucfirst(str_replace('_', ' ', $this->sync_type ?? 'Unknown')),
        };
    }

    // Scopes
    public function scopeForSupplier($query, int $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function scopeOfType($query, string $syncType)
    {
        return $query->where('sync_type', $syncType);
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('started_at', '>=', now()->subHours($hours));
    }

    // Methods
    public static function startLog(int $supplierId, string $syncType, string $direction = 'outbound', ?int $triggeredBy = null): self
    {
        return self::create([
            'supplier_id' => $supplierId,
            'sync_type' => $syncType,
            'direction' => $direction,
            'status' => 'started',
            'started_at' => now(),
            'triggered_by' => $triggeredBy ?? auth()->id(),
            'trigger_type' => $triggeredBy ? 'manual' : 'scheduler',
        ]);
    }

    public function markSuccess(array $stats = []): void
    {
        $this->update(array_merge([
            'status' => 'success',
            'completed_at' => now(),
            'total_duration_ms' => now()->diffInMilliseconds($this->started_at),
        ], $stats));
    }

    public function markFailed(string $errorMessage, ?string $errorCode = null): void
    {
        $this->update([
            'status' => 'failed',
            'completed_at' => now(),
            'error_message' => $errorMessage,
            'error_code' => $errorCode,
            'total_duration_ms' => now()->diffInMilliseconds($this->started_at),
        ]);
    }

    public function logRequest(array $data, ?array $headers = null): void
    {
        $this->update([
            'request_data' => $data,
            'request_headers' => $headers,
        ]);
    }

    public function logResponse(array $data, int $statusCode, int $durationMs): void
    {
        $this->update([
            'response_data' => $data,
            'http_status_code' => $statusCode,
            'duration_ms' => $durationMs,
        ]);
    }
}
