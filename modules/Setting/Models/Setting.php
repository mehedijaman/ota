<?php

namespace Modules\Setting\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Modules\Setting\Database\Factories\SettingFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\User\Models\User;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Setting extends BaseModel implements HasMedia
{
    use ActivityLog, HasFactory, InteractsWithMedia;

    protected $table = 'settings';

    protected $fillable = [
        'uuid',
        'group',
        'key',
        'value',
        'type',
        'context',
        'is_encrypted',
        'is_public',
        'is_active',
        'label',
        'description',
        'created_by',
        'updated_by',
    ];

    protected $appends = ['typed_value'];

    protected function casts(): array
    {
        return [
            'is_encrypted' => 'boolean',
            'is_public' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Setting $setting) {
            if (empty($setting->uuid)) {
                $setting->uuid = (string) Str::uuid();
            }
        });

        static::saved(function () {
            static::clearSettingsCache();
        });

        static::deleted(function () {
            static::clearSettingsCache();
        });
    }

    public static function clearSettingsCache(): void
    {
        Cache::forget('settings');
    }

    protected static function newFactory(): Factory
    {
        return SettingFactory::new();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
        $this->addMediaCollection('favicon')->singleFile();
        $this->addMediaCollection('banner')->singleFile();
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
     * Get the typed value based on the type column.
     */
    protected function typedValue(): Attribute
    {
        return Attribute::make(
            get: function () {
                $value = $this->is_encrypted ? $this->decryptValue($this->value) : $this->value;

                return match ($this->type) {
                    'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
                    'integer' => (int) $value,
                    'json', 'array' => json_decode($value, true) ?? [],
                    default => $value,
                };
            }
        );
    }

    /**
     * Set value with proper encoding based on type.
     */
    public function setTypedValue(mixed $value): void
    {
        if (is_array($value) || $this->type === 'json') {
            $value = json_encode($value);
        } elseif (is_bool($value)) {
            $value = $value ? '1' : '0';
        }

        if ($this->is_encrypted) {
            $value = $this->encryptValue($value);
        }

        $this->value = $value;
    }

    protected function encryptValue(string $value): string
    {
        return encrypt($value);
    }

    protected function decryptValue(?string $value): ?string
    {
        if (empty($value)) {
            return $value;
        }

        try {
            return decrypt($value);
        } catch (\Exception) {
            return $value;
        }
    }

    /**
     * Scope to filter by group.
     */
    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Scope to filter by context.
     */
    public function scopeContext($query, string $context = 'global')
    {
        return $query->where('context', $context);
    }

    /**
     * Scope to get only public settings.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope to get only active settings.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get a single setting value by group and key.
     */
    public static function getValue(string $group, string $key, mixed $default = null, string $context = 'global'): mixed
    {
        $setting = static::query()
            ->where('group', $group)
            ->where('key', $key)
            ->where('context', $context)
            ->first();

        return $setting?->typed_value ?? $default;
    }

    /**
     * Set a single setting value.
     */
    public static function setValue(string $group, string $key, mixed $value, array $options = []): static
    {
        $setting = static::updateOrCreate(
            [
                'group' => $group,
                'key' => $key,
                'context' => $options['context'] ?? 'global',
            ],
            [
                'type' => $options['type'] ?? static::inferType($value),
                'is_encrypted' => $options['is_encrypted'] ?? false,
                'is_public' => $options['is_public'] ?? false,
                'is_active' => $options['is_active'] ?? true,
                'label' => $options['label'] ?? null,
                'description' => $options['description'] ?? null,
            ]
        );

        $setting->setTypedValue($value);
        $setting->save();

        return $setting;
    }

    /**
     * Get all settings for a group as key-value pairs.
     */
    public static function getGroup(string $group, string $context = 'global'): array
    {
        return static::query()
            ->where('group', $group)
            ->where('context', $context)
            ->where('is_active', true)
            ->get()
            ->pluck('typed_value', 'key')
            ->toArray();
    }

    /**
     * Get all settings as a nested array [group => [key => value]].
     */
    public static function getAllCached(string $context = 'global'): array
    {
        return Cache::rememberForever('settings', function () use ($context) {
            return static::query()
                ->where('context', $context)
                ->where('is_active', true)
                ->get()
                ->groupBy('group')
                ->map(fn ($items) => $items->pluck('typed_value', 'key'))
                ->toArray();
        });
    }

    /**
     * Get public settings only (safe for frontend).
     */
    public static function getPublicSettings(string $context = 'global'): array
    {
        return static::query()
            ->where('context', $context)
            ->where('is_active', true)
            ->where('is_public', true)
            ->get()
            ->groupBy('group')
            ->map(fn ($items) => $items->pluck('typed_value', 'key'))
            ->toArray();
    }

    /**
     * Infer type from value.
     */
    protected static function inferType(mixed $value): string
    {
        return match (true) {
            is_bool($value) => 'boolean',
            is_int($value) => 'integer',
            is_array($value) => 'json',
            str_starts_with((string) $value, '#') && strlen($value) <= 9 => 'color',
            default => 'string',
        };
    }

    /**
     * Bulk set multiple settings.
     *
     * @param  array<string, array<string, mixed>>  $settings  [group => [key => value]]
     */
    public static function setMany(array $settings, array $options = []): void
    {
        foreach ($settings as $group => $keys) {
            foreach ($keys as $key => $value) {
                static::setValue($group, $key, $value, $options);
            }
        }
    }
}
