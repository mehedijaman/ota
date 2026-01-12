<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    protected $table = 'social_accounts';

    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
        'email',
        'name',
        'avatar',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
