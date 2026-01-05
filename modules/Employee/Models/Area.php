<?php

namespace Modules\Employee\Models;

use App\Models\User;
use App\Traits\HasUuid;
use App\Traits\SoftUserStamp;
use App\Traits\UserStamp;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Employee\Database\Factories\AreaFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;

class Area extends BaseModel
{
    use ActivityLog, HasFactory, Searchable;
    use HasUuid;
    use SoftDeletes, SoftUserStamp, UserStamp;

    protected $table = 'employee_areas';

    protected $fillable = [
        'uuid',
        'name',
        'code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function newFactory(): Factory
    {
        return AreaFactory::new();
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function employeeCount()
    {
        return $this->employees()->count();
    }

    /**
     * Generate a unique Area code.
     */
    public static function generateAreaCode(): string
    {
        $prefix = 'AR';
        $lastCode = static::where('code', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('code');

        $lastNumber = 0;
        if ($lastCode && preg_match('/\d+$/', $lastCode, $matches)) {
            $lastNumber = (int) $matches[0];
        }

        do {
            $lastNumber++;
            $newCode = $prefix.str_pad($lastNumber, 2, '0', STR_PAD_LEFT);
        } while (static::where('code', $newCode)->exists());

        return $newCode;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Area $area) {
            if (empty(trim($area->code ?? ''))) {
                $area->code = static::generateAreaCode();
            } else {
                $area->code = strtoupper(trim($area->code));
            }
        });

        static::updating(function (Area $area) {
            if ($area->isDirty('code') && ! empty(trim($area->code ?? ''))) {
                $area->code = strtoupper(trim($area->code));
            }
        });
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
