<?php
namespace App\Http\Controllers\API\ReturnUsers;

use App\Http\Controllers\API\FileController as FileController;
use App\UserProfile;

class ReturnUserProfileController
{
    public static function getUserProfile($userId){
        return UserProfile::where('userId', $userId)->first();
    }

    public static function getUserProfileById($id, $userId){
        return UserProfile::where(['id' => $id, 'userId' => $userId])->first();
    }

    public static function createProfile($data){ 
        $imageName = NULL;
        if(!empty($data['image_base64'])){
            $imageName = FileController::uploadFile($data['image_base64'], "feed/user-".$data['userId']."/profile");
        }         
        return UserProfile::updateOrcreate([
            'userId' => $data['userId'],
        ],
        [
            'userName' => $data['userName'], 
            'imageName' => $imageName, 
        ]);
    }

    public static function updateProfile($data){    
        $profile = [
            'userName' => $data['userName'], 
            'imageName' => null,
        ];  
        if(!empty($data['image_base64'])){
            $imageName = FileController::uploadFile($data['image_base64'], "feed/user-".$data['userId']."/profile");
            $profile['imageName'] = $imageName;
        }     
        UserProfile::where(['id' => $data['id'], 'userId' => $data['userId']])
                    ->update($profile);
        return self::getUserProfile($data['userId']);
    }
}