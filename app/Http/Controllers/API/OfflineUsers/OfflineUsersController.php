<?php

namespace App\Http\Controllers\API\OfflineUsers;

use App\OfflineUser;

class OfflineUsersController 
{
    public static function getOfflineUserByUserId($userId){
        return OfflineUser::where('userId', $userId)->first();
    }
}