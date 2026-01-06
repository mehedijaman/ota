<?php

namespace Modules\Sms\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Modules\Support\Models\BaseModel;
use Modules\User\Models\User;

class SmsTemplate extends BaseModel
{
    protected $table = 'sms_templates';

    protected $fillable = [
        'name',
        'slug',
        'content',
        'variables',
        'category',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'variables' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $template) {
            if (empty($template->slug)) {
                $template->slug = Str::slug($template->name);
            }
        });

        static::updating(function (self $template) {
            if ($template->isDirty('name') && empty($template->slug)) {
                $template->slug = Str::slug($template->name);
            }
        });
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
     * Parse template content and replace placeholders with values.
     *
     * @param  array<string, string>  $data
     */
    public function parse(array $data = []): string
    {
        $content = $this->content;

        foreach ($data as $key => $value) {
            $content = str_replace('{{'.$key.'}}', (string) $value, $content);
            $content = str_replace('{{ '.$key.' }}', (string) $value, $content);
        }

        return $content;
    }

    /**
     * Extract variable placeholders from the template content.
     *
     * @return array<int, string>
     */
    public function extractVariables(): array
    {
        preg_match_all('/\{\{\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*\}\}/', $this->content, $matches);

        return array_unique($matches[1] ?? []);
    }
}
