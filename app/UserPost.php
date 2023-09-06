<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Askedio\SoftCascade\Traits\SoftCascadeTrait; 

class UserPost extends Model
{
    use SoftDeletes, SoftCascadeTrait;

    protected $softCascade  = ['media', 'postCategory', 'likes', 'comments', 'reports'];

    protected $fillable = [
        'userId', 'regionId', 'postText', 'status', 'postNumber', 'likesNumber', 'commentsNumber', 'reportsNumber',
    ];
    //satus: (pending, approved, rejected, reported) 
    protected $hidden = ['deleted_at'];

    protected $casts = [];

    public function media(){
        return $this->hasMany('App\UserPostMedia', 'userPostId', 'id');
    }

    public function likes(){
        return $this->hasMany('App\UserPostLike', 'userPostId', 'id');
    }

    public function comments(){
        return $this->hasMany('App\UserPostComment', 'userPostId', 'id');
    }

    public function reports(){
        return $this->hasMany('App\UserPostReport', 'userPostId', 'id');
    }

    public function notifications(){
        return $this->hasMany('App\UserPostNotification', 'userPostId', 'id');
    }

    public function postCategory(){
        return $this->hasOne('App\UserPostCategory', 'userPostId', 'id');
    }

    public function category(){
        return $this->hasOneThrough('App\PostCategory', 'App\UserPostCategory', 'userPostId', 'id', 'id', 'postCategoryId');
    }

    public function profile(){
        return $this->belongsTo('App\UserProfile', 'userId', 'userId');
    }

    public function community(){
        return $this->belongsTo('App\Region', 'regionId', 'id')->select('id', 'name', 'nameAr', 'nameFr', 'nameUr', 'nameIn');
    }
}
