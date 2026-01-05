<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportLog extends Model
{
    protected $fillable = [
        'file_name',
        'import_type',
        'status',
        'summary',
        'error_message',
        'uploaded_by',
    ];
}
