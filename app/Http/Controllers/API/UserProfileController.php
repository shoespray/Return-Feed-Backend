<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Validations\UserProfileValidationController as UserProfileValidationController;
use App\Http\Controllers\API\ReturnUsers\ReturnUserProfileController as ReturnUserProfileController;
use Illuminate\Http\Request;
use Exception;

class UserProfileController extends BaseController 
{
    public function addProfile(Request $request){
        try{
            $validator = UserProfileValidationController::validateAddProfile($request);
            if(!$validator['isValid']){
                return $this->sendError('Validation Error: '.$validator['errorMessage'],'Validation Error.'); 
            } 
            if(!empty(ReturnUserProfileController::getUserProfile(auth()->id()))){
                return $this->sendError('A profile was already created for this user','A profile was already created for this user', 400);
            }
            $message = ReturnUserProfileController::checkUserName($request->userName);
            if(!empty($message)){
                return $this->sendError($message, $message, 400);
            }
            $profile = ReturnUserProfileController::createProfile([
                'userId' => auth()->id(),
                'userName' => $request->userName,
                'image_base64' => $request->image_base64,
            ]);
            return $this->sendResponse($profile, 'User profile created');
        } catch (Exception $e) {
            return $this->sendError('Exception', $e->getMessage(), 400);
        }
    }

    public function updateProfile(Request $request){
        try{
            $validator = UserProfileValidationController::validateUpdateProfile($request);
            if(!$validator['isValid']){
                return $this->sendError('Validation Error: '.$validator['errorMessage'],'Validation Error.'); 
            } 
            if(empty(ReturnUserProfileController::getUserProfileById($request->id, auth()->id()))){
                return $this->sendError('Profile does not exist','Profile does not exist', 400);
            }
            $message = ReturnUserProfileController::checkUserName($request->userName);
            if(!empty($message)){
                return $this->sendError($message, $message, 400);
            }
            $profile = ReturnUserProfileController::updateProfile([
                'id' => $request->id,
                'userId' => auth()->id(),
                'userName' => $request->userName,
                'image_base64' => $request->image_base64,
                'isImageRemoved' => $request->isImageRemoved,
            ]);
            return $this->sendResponse($profile, 'User profile updated');
        } catch (Exception $e) {
            return $this->sendError('Exception', $e->getMessage(), 400);
        }
    }
}