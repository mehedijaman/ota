<?php

namespace Modules\Setting\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Setting\Database\Factories\AISettingFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;

class AISetting extends BaseModel
{
    use ActivityLog, HasFactory;

    protected $table = 'ai_settings';

    protected $hidden = ['api_key'];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'api_key' => 'encrypted',
        ];
    }

    protected static function newFactory(): Factory
    {
        return AISettingFactory::new();
    }

    public static function getDefault(): ?self
    {
        return self::where('is_default', true)->first();
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function markAsDefault(): void
    {
        self::where('is_default', true)->update(['is_default' => false]);
        $this->update(['is_default' => true]);
    }
}
