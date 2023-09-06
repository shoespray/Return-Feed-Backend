<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPostNotification extends Model
{
    use SoftDeletes;

    protected $softCascade  = [];

    protected $fillable = [
        'toUserId', 'userPostId', 'fromUserId', 'notification', 'notificationAr', 'notificationFr', 'notificationUr', 'notificationIn',
        'type', 'isRead',
    ];
    //type = (commented, liked, approved, reported)
    protected $hidden = ['updated_at', 'deleted_at'];

    protected $casts = [];

    protected $table = 'user_post_notifications';

    public function post(){
        return $this->hasOne('App\UserPost', 'id', 'userPostId');
    }

    public function fromProfile(){
        return $this->hasOne('App\UserProfile', 'userId', 'fromUserId');
    }
}
