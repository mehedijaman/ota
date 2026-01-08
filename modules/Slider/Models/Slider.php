<?php

namespace Modules\Slider\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Slider\Database\Factories\SliderFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Slider extends BaseModel implements HasMedia
{
    use ActivityLog, HasFactory, InteractsWithMedia, Searchable, SoftDeletes;

    protected $table = 'sliders';

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $appends = ['image_url'];

    /**
     * Register the media collections for the slider.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }

    /**
     * Get the URL of the slider image.
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('image') ?: null;
    }

    protected static function newFactory(): Factory
    {
        return SliderFactory::new();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
