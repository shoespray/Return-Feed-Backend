<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfflineUser extends Model
{
    use SoftDeletes;

    protected $softCascade  = [];
    
    protected $fillable = [
        'accountNumber', 'refNumber', 'fullName', 'pin', 'activationDate', 'regionId', 'defaultPin',
        'userId', 'activated', 'firstInspectionDate', 'isAdmin',
    ];

    protected $hidden = ['defaultPin', 'pin', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [];

    protected $table = 'offline_users';

    public function returnUser(){
        return $this->belongsTo('App\ReturnUser','userId', 'id');
    }
    
    public function region(){
        return $this->belongsTo('App\Region', 'regionId', 'id');
    }
}
