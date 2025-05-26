<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPlatform extends Model
{
    protected $table = 'user_platforms';

    protected $fillable = [
        'user_id',
        'platform_id',
    ];
}