<?php

namespace Modules\Faq\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Faq\Database\Factories\FaqFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;

class Faq extends BaseModel
{
    use ActivityLog, HasFactory, HasUuid, Searchable, SoftDeletes;

    protected $table = 'faqs';

    protected $fillable = [
        'faq_category_id',
        'question',
        'answer',
        'status',
        'sort_order',
        'is_featured',
        'target_audience',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(FaqCategory::class, 'faq_category_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    protected static function newFactory(): Factory
    {
        return FaqFactory::new();
    }
}
