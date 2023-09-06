<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Askedio\SoftCascade\Traits\SoftCascadeTrait; 

class UserPostComment extends Model
{
    use SoftDeletes;

    protected $softCascade  = [];

    protected $fillable = [
        'userPostId', 'commentedByUserId', 'comment',
    ];

    protected $hidden = ['updated_at', 'deleted_at'];

    protected $casts = [];

    protected $table = 'user_post_comments';

    public function profile(){
        return $this->belongsTo('App\UserProfile', 'commentedByUserId', 'userId');
    }
}
