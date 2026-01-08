<?php

namespace Modules\Page\Models;

use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Page\Database\Factories\PageFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Page extends BaseModel implements HasMedia
{
    use ActivityLog, HasFactory, InteractsWithMedia, Searchable, Sluggable, SoftDeletes;

    protected $table = 'pages';

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $appends = ['image_url'];

    protected $casts = [
        'published_at' => 'date',
        'active' => 'boolean',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured_image')->singleFile();
    }

    public function getPublishedAtFormattedAttribute(): ?string
    {
        return $this->published_at ? $this->published_at->format('d/m/Y') : null;
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
            ],
        ];
    }

    public function getStatusAttribute(): string
    {
        if ($this->published_at && Carbon::now()->greaterThan($this->published_at)) {
            return 'Published';
        }

        return 'Draft';
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('featured_image') ?: null;
    }

    protected static function newFactory(): Factory
    {
        return PageFactory::new();
    }

    public function parent()
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }
}
