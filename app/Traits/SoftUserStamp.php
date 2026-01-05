<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait SoftUserStamp
{
    protected static function bootSoftUserStamp()
    {
        // Automatically set `deleted_by` on delete (soft delete)
        static::deleting(function ($model) {
            if (Auth::check() && in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($model))) {
                $model->deleted_by = Auth::id();
                $model->save();  // Save before soft delete
            }
        });

        // Reset `deleted_by` when the model is restored from soft delete
        static::restoring(function ($model) {
            if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($model))) {
                $model->deleted_by = null;  // Reset the deleted_by field
                if (Auth::check()) {
                    $model->updated_by = Auth::id();  // Optionally update the updated_by field
                }
            }
        });
    }
}
