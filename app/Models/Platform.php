<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Platform extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_platforms')
                    ->withTimestamps();
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_platforms')
                    ->withPivot('platform_status')
                    ->withTimestamps();
    }
}