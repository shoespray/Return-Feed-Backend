<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPostLike extends Model
{
    use SoftDeletes;

    protected $softCascade  = [];

    protected $fillable = [
        'userPostId', 'likedByUserId', 
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $casts = [];

    protected $table = 'user_post_likes';
}
