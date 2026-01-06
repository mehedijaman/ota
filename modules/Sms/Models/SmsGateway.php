<?php

namespace Modules\Sms\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Support\Models\BaseModel;
use Modules\User\Models\User;

class SmsGateway extends BaseModel
{
    protected $table = 'sms_gateways';

    protected $fillable = [
        'name',
        'provider_class',
        'credentials',
        'is_default',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'credentials' => 'array',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the default active gateway.
     */
    public static function getDefault(): ?self
    {
        return self::query()
            ->where('is_default', true)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get an active gateway (default preferred, else any active).
     */
    public static function getActive(): ?self
    {
        $default = self::getDefault();

        if ($default) {
            return $default;
        }

        return self::query()->where('is_active', true)->first();
    }

    /**
     * Get all available provider classes from config.
     *
     * @return array<string, string>
     */
    public static function getAvailableProviders(): array
    {
        $providersConfig = config('sms.providers', []);
        $providers = [];

        foreach (array_keys($providersConfig) as $providerClass) {
            $providers[$providerClass] = class_basename($providerClass);
        }

        return $providers;
    }

    /**
     * Get credential fields for a provider class.
     *
     * @return array<int, string>
     */
    public static function getProviderFields(string $providerClass): array
    {
        $providersConfig = config('sms.providers', []);

        if (! isset($providersConfig[$providerClass])) {
            return [];
        }

        return array_keys($providersConfig[$providerClass]);
    }
}
