<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailedImport extends Model
{
    protected $fillable = [
        'import_type',
        'row_data',
        'reason',
        'uploaded_by',
    ];

    protected $casts = [
        'row_data' => 'array', // auto-cast JSON into array
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
