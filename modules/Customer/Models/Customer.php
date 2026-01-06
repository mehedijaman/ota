<?php

namespace Modules\Customer\Models;

use App\Models\User;
use App\Traits\HasUuid;
use App\Traits\SoftUserStamp;
use App\Traits\UserStamp;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Customer\Database\Factories\CustomerFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Customer extends BaseModel implements HasMedia
{
    use ActivityLog, HasFactory, HasUuid, Searchable;
    use InteractsWithMedia;
    use SoftDeletes, SoftUserStamp, UserStamp;

    protected $table = 'customers';

    protected $appends = ['avatar_url', 'full_name', 'has_user'];

    protected $casts = [
        'date_of_birth' => 'date',
        'passport_expiry' => 'date',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')->singleFile();

        $this->addMediaCollection('nid_front')->singleFile();
        $this->addMediaCollection('nid_back')->singleFile();
        $this->addMediaCollection('passport')->singleFile();
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('avatar') ?: null;
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function getHasUserAttribute(): bool
    {
        return \Modules\User\Models\User::where('profile_type', 'customer')
            ->where('profile_id', $this->id)
            ->exists();
    }

    public function getDocumentsAttribute(): array
    {
        $documentTypes = ['nid_front', 'nid_back', 'passport'];
        $documents = [];

        foreach ($documentTypes as $type) {
            $media = $this->getFirstMedia($type);
            if ($media) {
                $documents[] = [
                    'id' => $media->id,
                    'type' => $type,
                    'type_label' => $this->getDocumentTypeLabel($type),
                    'name' => $media->file_name,
                    'mime_type' => $media->mime_type,
                    'size' => $media->size,
                    'size_formatted' => $this->formatFileSize($media->size),
                    'url' => $media->getUrl(),
                    'created_at' => $media->created_at->format('d M Y'),
                ];
            }
        }

        return $documents;
    }

    protected function getDocumentTypeLabel(string $type): string
    {
        return match ($type) {
            'nid_front' => 'NID Front',
            'nid_back' => 'NID Back',
            'passport' => 'Passport',
            default => ucfirst(str_replace('_', ' ', $type)),
        };
    }

    protected function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2).' KB';
        }

        return $bytes.' bytes';
    }

    protected static function newFactory(): Factory
    {
        return CustomerFactory::new();
    }

    public function user()
    {
        return $this->morphOne(User::class, 'profile');
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

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
