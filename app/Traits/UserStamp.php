<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait UserStamp
{
    protected static function bootUserStamp()
    {
        // Automatically set `created_by` and `updated_by` on creation
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        // Automatically set `updated_by` on updates
        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }
}
