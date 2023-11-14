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

    public static function checkIfUserNameExists($userId, $userName){
        return UserProfile::where('userName', $userName)->where('userId', '<>',$userId)->exists();
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
            'isAdmin' => $data['isAdmin'], 
            'imageName' => $imageName, 
        ]);
    }

    public static function updateProfile($data){    
        $profile = [
            'userName' => $data['userName'], 
        ];  
        if($data['isImageRemoved']){
            $profile['imageName'] = null;
        }
        if(!empty($data['image_base64']) && !$data['isImageRemoved']){
            $imageName = FileController::uploadFile($data['image_base64'], "feed/user-".$data['userId']."/profile");
            $profile['imageName'] = $imageName;
        }     
        UserProfile::where(['id' => $data['id'], 'userId' => $data['userId']])
                    ->update($profile);
        return self::getUserProfile($data['userId']);
    }

    public static function checkUserName($userId, $userName){
        if(self::checkIfUserNameExists($userId, $userName)){
            return 'This username is taken, please choose another one';
        }
        if(strpos(strtoupper($userName), "NADEERA") !== false || strpos(strtoupper($userName), "YALLARETURN") 
            || strpos(strtoupper($userName), "YALLA RETURN") !== false){
            return 'This username is taken, please choose another one';
        }
        if(preg_match('/[^a-z_ \-0-9]/i', $userName)){
            return 'Username cannot contain special characters. Only letters, spaces, and numbers are allowed';
        }
        return '';
    }
}