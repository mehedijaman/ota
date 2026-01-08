<?php

namespace Modules\Testimonial\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;
use Modules\Testimonial\Database\Factories\TestimonialFactory;

class Testimonial extends BaseModel
{
    use ActivityLog, HasFactory, Searchable, SoftDeletes;

    protected $table = 'testimonials';

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $appends = ['image_url'];

    protected $casts = [
        'active' => 'boolean',
        'featured' => 'boolean',
    ];

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return asset("storage/testimonial/{$this->image}");
        }

        return null;
    }

    protected static function newFactory(): Factory
    {
        return TestimonialFactory::new();
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
