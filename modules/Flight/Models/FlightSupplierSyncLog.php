<?php

namespace Modules\Flight\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Flight\Database\Factories\FlightSupplierSyncLogFactory;
use Modules\Support\Models\BaseModel;

class FlightSupplierSyncLog extends BaseModel
{
    use HasFactory;

    protected $table = 'flight_supplier_sync_logs';

    protected function casts(): array
    {
        return [
            'total_records' => 'integer',
            'created_count' => 'integer',
            'updated_count' => 'integer',
            'skipped_count' => 'integer',
            'failed_count' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'duration_seconds' => 'integer',
            'request_data' => 'array',
            'response_summary' => 'array',
            'errors' => 'array',
        ];
    }

    protected static function newFactory(): FlightSupplierSyncLogFactory
    {
        return FlightSupplierSyncLogFactory::new();
    }

    // Relationships
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(FlightSupplier::class, 'supplier_id');
    }

    public function initiatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    // Accessors
    public function getTypeDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->type));
    }

    public function getStatusDisplayAttribute(): string
    {
        return ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'secondary',
            'running' => 'info',
            'completed' => 'success',
            'failed' => 'danger',
            'partial' => 'warning',
            default => 'secondary',
        };
    }

    public function getDurationDisplayAttribute(): string
    {
        if (! $this->duration_seconds) {
            return 'N/A';
        }

        if ($this->duration_seconds < 60) {
            return "{$this->duration_seconds}s";
        }

        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;

        return "{$minutes}m {$seconds}s";
    }

    public function getSuccessRateAttribute(): float
    {
        if ($this->total_records === 0) {
            return 0;
        }

        $successful = $this->created_count + $this->updated_count;

        return round($successful / $this->total_records * 100, 1);
    }

    public function getProcessedCountAttribute(): int
    {
        return $this->created_count + $this->updated_count + $this->skipped_count + $this->failed_count;
    }

    // Scopes
    public function scopeForSupplier($query, int $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeRunning($query)
    {
        return $query->where('status', 'running');
    }

    public function scopeRecent($query)
    {
        return $query->orderByDesc('started_at');
    }

    // Methods
    public function isRunning(): bool
    {
        return $this->status === 'running';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function hasErrors(): bool
    {
        return ! empty($this->errors);
    }

    public function start(): void
    {
        $this->update([
            'status' => 'running',
            'started_at' => now(),
        ]);
    }

    public function complete(): void
    {
        $this->update([
            'status' => $this->failed_count > 0 && $this->created_count + $this->updated_count === 0 ? 'failed' : ($this->failed_count > 0 ? 'partial' : 'completed'),
            'completed_at' => now(),
            'duration_seconds' => now()->diffInSeconds($this->started_at),
        ]);
    }

    public function fail(array $errors = []): void
    {
        $this->update([
            'status' => 'failed',
            'completed_at' => now(),
            'duration_seconds' => now()->diffInSeconds($this->started_at),
            'errors' => $errors,
        ]);
    }
}
