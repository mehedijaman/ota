<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    /**
     * Boot the trait and assign a UUID when creating a model.
     */
    protected static function bootHasUuid()
    {
        static::creating(function ($model) {
            // Only assign UUID if the column exists and is empty
            if ($model->hasColumn('uuid') && empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Override the route key name to use UUID if the column exists.
     */
    public function getRouteKeyName()
    {
        return $this->hasColumn('uuid') ? 'uuid' : $this->getKeyName();
    }

    /**
     * Check if the model has a column.
     */
    public function hasColumn($column)
    {
        return in_array($column, $this->getConnection()
            ->getSchemaBuilder()
            ->getColumnListing($this->getTable()));
    }
}
