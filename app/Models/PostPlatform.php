<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostPlatform extends Model
{
    protected $table = 'post_platforms';

    protected $fillable = [
        'post_id',
        'platform_id',
        'platform_status',
    ];

    protected $casts = [
        'platform_status' => 'string',
    ];
}