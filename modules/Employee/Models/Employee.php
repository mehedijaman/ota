<?php

namespace Modules\Employee\Models;

use App\Models\User;
use App\Traits\HasUuid;
use App\Traits\SoftUserStamp;
use App\Traits\UserStamp;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Employee\Database\Factories\EmployeeFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Employee extends BaseModel implements HasMedia
{
    use ActivityLog, HasFactory, Searchable;
    use HasUuid;
    use InteractsWithMedia;
    use SoftDeletes, SoftUserStamp, UserStamp;

    protected $table = 'employees';

    protected $appends = ['photo_url', 'service_length'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')->singleFile();

        // Document collections
        $this->addMediaCollection('nid_card')->singleFile();
        $this->addMediaCollection('passport')->singleFile();
        $this->addMediaCollection('driving_licence')->singleFile();
        $this->addMediaCollection('tin_certificate')->singleFile();
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('photo') ?: null;
    }

    /**
     * Get all documents with their metadata
     */
    public function getDocumentsAttribute(): array
    {
        $documentTypes = ['nid_card', 'passport', 'driving_licence', 'tin_certificate'];
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
            'nid_card' => 'NID Card',
            'passport' => 'Passport',
            'driving_licence' => 'Driving Licence',
            'tin_certificate' => 'TIN Certificate',
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
        return EmployeeFactory::new();
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function user()
    {
        return $this->morphOne(User::class, 'profile');
    }

    public function getServiceLengthAttribute()
    {
        if (! $this->joining_date) {
            return null;
        }

        try {
            $from = $this->joining_date instanceof Carbon
                ? $this->joining_date
                : Carbon::parse($this->joining_date);

            // Check if resignation exists and has a date
            $to = $this->resign && $this->resign->date
                ? Carbon::parse($this->resign->date)
                : now();

            return $from->diff($to)->format('%y years, %m months');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Generate a unique employee code.
     */
    public static function generateEmployeeCode(): string
    {
        $prefix = 'EMP-';

        $lastCode = static::where('code', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('code');

        $lastNumber = 0;
        if ($lastCode && preg_match('/\d+$/', $lastCode, $matches)) {
            $lastNumber = (int) $matches[0];
        }

        do {
            $lastNumber++;
            $newEmployeeNo = $prefix.str_pad($lastNumber, 5, '0', STR_PAD_LEFT);
        } while (static::where('code', $newEmployeeNo)->exists());

        return $newEmployeeNo;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Employee $employee) {
            if (empty(trim($employee->code ?? ''))) {
                $employee->code = static::generateEmployeeCode();
            } else {
                $employee->code = strtoupper(trim($employee->code));
            }
        });

        // Optional: prevent accidental code change on update
        static::updating(function (Employee $employee) {
            if (! $employee->isDirty('code')) {
                return;
            }
            // If code is being changed, normalize it
            if (! empty(trim($employee->code ?? ''))) {
                $employee->code = strtoupper(trim($employee->code));
            }
        });
    }

    public function bankAccount()
    {
        return $this->hasOne(EmployeeBankAccount::class);
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

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
