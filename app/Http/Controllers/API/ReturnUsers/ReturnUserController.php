<?php

namespace App\Http\Controllers\API\ReturnUsers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\API\AuthController as AuthController;
use App\Http\Controllers\API\NumberGeneratorController as NumberGeneratorController;
use App\Http\Controllers\API\RegionController as RegionController;
use App\Http\Controllers\API\ReturnUsers\ReturnUserDetailsController as ReturnUserDetailsController;
use App\Http\Controllers\API\ReturnUsers\ReturnUserProfileController as ReturnUserProfileController;
use App\Http\Controllers\API\SmartBins\SmartBinsController as SmartBinsController;
use App\Http\Controllers\API\BoutiqueUsers\BoutiqueUserController as BoutiqueUserController;
use App\Http\Controllers\API\OfflineUsers\OfflineUsersController as OfflineUsersController;
use App\Http\Controllers\API\BoutiqueUsers\BoutiqueUserEmailVerificationController as BoutiqueUserEmailVerificationController;
use App\Http\Controllers\API\TransactionController as TransactionController;
use App\Http\Controllers\API\BoutiqueUsers\BoutiqueUserWasteWeightController as BoutiqueUserWasteWeightController;
use App\ReturnUser;

class ReturnUserController
{
    public static function getUserWithToken($user){
        $token = JWTAuth::fromUser($user);
        $response = AuthController::createNewToken($token);
        $response['user'] = self::userResponse($user);
        return $response;
    }

    public static function getUserWithRefreshToken($user){
        $response = AuthController::refresh();
        $response['user'] = self::userResponse($user);
        return $response;
    }
    
    protected static function userResponse($mainUser){     
        $passwordExists = true;   
        $mainUser = self::getUserWithDetailsById($mainUser->id);
        $profile = ReturnUserProfileController::getUserProfile($mainUser->id);
        $creditByRegion = ReturnUserDetailsController::getUserCreditByRegion([
                            'userId' => $mainUser->id,
                            'regionId' => $mainUser->regionId,
                        ]);
        $smartBin = SmartBinsController::getUserSmartBinUUIDs($mainUser->regionId);
        if($mainUser->userType == 'online'){
            $onlineUser = BoutiqueUserController::getBoutiqueUserByUserId($mainUser->id); 
            if(!empty($onlineUser->email)){
                $emailUser = BoutiqueUserEmailVerificationController::getEmailVerification($onlineUser->email);
                if(empty($emailUser->password))
                    $passwordExists = false; 
            }   
                             
        }
        else {
            $offlineUser = OfflineUsersController::getOfflineUserByUserId($mainUser->id);
        }

        $latestTransaction = TransactionController::getLatestUserTransaction([
            'userType' => $mainUser->userType,
            'userId' => empty($onlineUser) ? $offlineUser->id : $onlineUser->id,
            'regionId' => $mainUser->regionId,
            'hasInspectionModule' => $mainUser->region->hasInspectionModule
        ]);
        
        $weightRecycled = $mainUser->region->hasInspectionModule ? $creditByRegion->cumulativeWeight
                            : BoutiqueUserWasteWeightController::getTotalWasteWeightByType([
                                'userType' => $mainUser->userType,
                                'userId' => empty($onlineUser) ? $offlineUser->id : $onlineUser->id, 
                                'regionId' => $mainUser->regionId,
                            ]);

        return [
            'id' => $mainUser->id,
            'account' => empty($onlineUser) ? $offlineUser->accountNumber : (!empty($onlineUser->email) ? $onlineUser->email : $onlineUser->phoneNumber),
            'userNumber' => empty($onlineUser) ? $offlineUser->accountNumber : $mainUser->userNumber ,
            'regionId' => $mainUser->regionId,
            'reference' => empty($onlineUser) ? $offlineUser->refNumber : $onlineUser->reference,
            'isVerified' => $mainUser->isVerified,
            'totalCredits' => ceil($creditByRegion->totalCredits),
            'avgRating' => round($creditByRegion->avgRating,2),
            'weightRecycled' => round($weightRecycled,2),
            'isNotificationOn' => $mainUser->isNotificationOn,
            'referralCode' => $mainUser->referralCode,
            'isOrganization' => empty($onlineUser) ? false : $onlineUser->isOrganization,
            'region' => $mainUser->region,
            'country' => $mainUser->country,
            'profile' => $profile,
            'loggedWith' => !empty($offlineUser) ? 'accountNumber' : (!empty($onlineUser->email) ? $onlineUser->provider : 'phoneNumber'),
            'passwordExists' => $passwordExists,
            'userType' => $mainUser->userType,
            'smartBin' => $smartBin,
            'latestTransaction' => $latestTransaction,
            'regionCounter' => RegionController::getRegionByCountry($mainUser->countryId)->count(),
        ];
    }    

    public static function getUserByNumber($userNumber){
        return ReturnUser::where('userNumber', $userNumber)->first();
    }
    public static function getUserById($id){
        return ReturnUser::where('id', $id)->first();
    }

    public static function getUserWithDetailsById($id){
        return ReturnUser::where('id', $id)->with('region')->with('country')->first();
    }

    public static function CurrentReturnUser(){
        return ReturnUser::where('id', auth()->id())->first();
    }
    
    public static function getUserByReferralCode($referralCode){
        return ReturnUser::where('referralCode', $referralCode)->first();
    }

    public static function getAllReturnUsers(){
        return ReturnUser::all();
    }

    
    public static function getLatestUser(){
        return ReturnUser::orderBy('id', 'desc')->first();
    }

    public static function create($user){
        $userNumber = self::generateUserNumber();
        $user = ReturnUser::create([                    
                    'userType' => $user['userType'], 
                    'regionId' => $user['regionId'], 
                    'countryId' => $user['countryId'], 
                    'userNumber' => $userNumber,
                    'isVerified' => true,
                    'totalCredits' => 0,
                    'isNotificationOn' => true,
                    'referralCode' => self::generateRandomReferralCode(6),
                    'isLoggedIn' => true,
                    'deviceToken' => $user['deviceToken'], 
                ]);
        $user->hashedId = Crypt::encryptString($user->id);
        $user->save();
        return $user;        
    }

    public static function updateIsVerified($user, $isVerified){
        $user->isVerified = $isVerified;
        $user->save();
        return $user;
    }

    public static function updateUserCredits($user, $totalCredit){
        $totalCredit = $user->totalCredits + $totalCredit;
        if($totalCredit < 0) $totalCredit = 0;
        $user->totalCredits = $totalCredit;
        $user->save();
    }

    public static function updateUserRegion($user, $regionId){
        $user->regionId = $regionId;
        $user->save();
        ReturnUserDetailsController::createUserCreditsIfNotExists([
            'userId' => $user->id,
            'regionId' => $user->regionId,
        ]);
        return self::userResponse($user);
    }

    public static function updateUserCommunity($user, $regionId){
        $user->regionId = $regionId;
        $user->save();
        ReturnUserDetailsController::updateUserCommunityCredits([
            'userId' => $user->id,
            'regionId' => $user->regionId,
        ]);
        return self::userResponse($user);
    }
    
    public static function updateAppInfo($user, $info){
        $user->appLanguage = strtoupper($info['appLanguage']);
        $user->appVersion = $info['appVersion'];
        $user->deviceType = $info['deviceType'];
        if(!empty($info['deviceToken'])){
            $user->deviceToken = $info['deviceToken'];
        }
        $user->deviceManufacturer = $info['deviceManufacturer'];
        $user->deviceModel = $info['deviceModel'];
        $user->systemVersion = $info['systemVersion'];
        $user->userAgent = $info['userAgent'];
        $user->deviceCountry = $info['deviceCountry'];
        $user->save();
    }
    public static function updateFCMToken($user, $deviceToken){
        $user->isLoggedIn = true;
        if(!empty($deviceToken)){
            $user->deviceToken = $deviceToken;
        }        
        $user->save();
    } 

    public static function updateReferralCount($user){
        $user->referralCount = $user->referralCount + 1;
        $user->save();
    }

    public static function deleteUserAccount($user){
        $user->isDeleted = true;
        $user->save();
    }

    public static function logout($user){
        $user->isLoggedIn = false;
        $user->save();
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    protected static function generateUserNumber(){
        $latestUser = self::getLatestUser();
        if(empty($latestUser)) $maxNumber = 10000;
        else $maxNumber = $latestUser->userNumber;
        $maxNumber += 1;
        return $maxNumber;
    }  

    protected static function generateRandomReferralCode($length){
        $referralCode = '';
        while(true){
            $referralCode = NumberGeneratorController::generateBase62Str(6);
            if(empty(self::getUserByReferralCode($referralCode))) break;
        }
        return $referralCode;
    }
}
