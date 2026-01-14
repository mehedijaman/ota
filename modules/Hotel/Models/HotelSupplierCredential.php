<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Support\Models\BaseModel;

class HotelSupplierCredential extends BaseModel
{
    use HasFactory;

    protected $table = 'hotel_supplier_credentials';

    protected $hidden = [
        'api_key',
        'api_secret',
        'password',
        'access_token',
        'refresh_token',
        'client_secret',
        'certificate',
        'private_key',
    ];

    protected function casts(): array
    {
        return [
            'api_key' => 'encrypted',
            'api_secret' => 'encrypted',
            'password' => 'encrypted',
            'access_token' => 'encrypted',
            'refresh_token' => 'encrypted',
            'client_secret' => 'encrypted',
            'certificate' => 'encrypted',
            'private_key' => 'encrypted',
            'token_expires_at' => 'datetime',
            'additional_config' => 'array',
            'is_active' => 'boolean',
            'last_used_at' => 'datetime',
            'last_success_at' => 'datetime',
            'last_failure_at' => 'datetime',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(HotelSupplier::class, 'supplier_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeProduction($query)
    {
        return $query->where('environment', 'production');
    }

    public function scopeSandbox($query)
    {
        return $query->where('environment', 'sandbox');
    }

    public function isTokenExpired(): bool
    {
        if (! $this->token_expires_at) {
            return true;
        }

        return $this->token_expires_at->isPast();
    }

    public function markAsUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    public function markSuccess(): void
    {
        $this->update([
            'last_used_at' => now(),
            'last_success_at' => now(),
        ]);
    }

    public function markFailure(string $error): void
    {
        $this->update([
            'last_used_at' => now(),
            'last_failure_at' => now(),
            'last_error' => $error,
        ]);
    }
}
