<?php
namespace App\Http\Controllers\API\Feeds;

use App\Http\Controllers\API\ReturnUsers\ReturnUserController as ReturnUserController;
use App\Http\Controllers\API\ReturnUsers\ReturnUserProfileController as ReturnUserProfileController;
use App\Http\Controllers\API\Feeds\PostMediaController as PostMediaController;
use App\Http\Controllers\API\FCMController as FCMController;
use App\UserPostNotification;

class PostNotificationController
{
    public static function getAllNotifications($data){
        $regionId = $data['regionId'];
        $notifications = UserPostNotification::where('toUserId', $data['toUserId'])
                ->whereHas('post', function($query) use ($regionId){
                    $query->where('regionId', $regionId);
                });
        if($data['status'] == 'unRead'){
            $notifications = $notifications->where('isRead', false);
        }
        $notifications = $notifications->with('post')->with('fromProfile')
                                        ->orderBy('created_at', 'desc')->paginate(25);
        return $notifications;
    }

    public static function getNotificationById($id){
        return UserPostNotification::where('id', $id)->first();
    }

    public static function getLikedNotificationByUser($userId, $userPostId){
        return UserPostNotification::where(['fromUserId' => $userId, 'userPostId' => $userPostId, 'type' => 'liked'])
                ->first();
    }

    public static function markAllAsRead($userId, $regionId){
        UserPostNotification::where(['toUserId' => $userId, 'isRead' => false])
                        ->whereHas('post', function($query) use ($regionId){
                            $query->where('regionId', $regionId);
                        })
                        ->update([
                            'isRead' => true
                        ]);
    }

    public static function markAsRead($notification){
        $notification->isRead = true;
        $notification->save();
        return $notification;
    }

    public static function addPostNotification($data){
        $notification = '';
        $notificationAr = '';
        $notificationFr = '';
        $notificationUr = '';
        $notificationIn = '';

        $title = 'New notification';
        $titleAr = 'إشعار جديد';
        $titleFr = 'Nouvelle notification';

        $profile = ReturnUserProfileController::getUserProfile($data['fromUserId']);
        if($data['type'] == 'commented'){            
            $notification = 'commented on your post';
            $notificationAr = 'أضاف تعليقاً على منشورك';
            $notificationFr = 'a commenté sur votre publication';
            $notificationUr = 'commented on your post';
            $notificationIn = 'commented on your post';
            $body = $profile->userName.' '.$notification;
            $bodyAr = $profile->userName.' '.$notificationAr;
            $bodyFr = $profile->userName.' '.$notificationFr;
        }
        if($data['type'] == 'liked'){
            $notification = 'liked your post';
            $notificationAr = 'اضاف اعجاباً على منشورك';
            $notificationFr = 'a aimé votre publication';
            $notificationUr = 'liked your post';
            $notificationIn = 'liked your post';
            $body = $profile->userName.' '.$notification;
            $bodyAr = $profile->userName.' '.$notificationAr;
            $bodyFr = $profile->userName.' '.$notificationFr;
        }
        if($data['type'] == 'reported'){
            $notification = 'reported your post';
            $notificationAr = 'قام بالإبلاغ عن منشورك';
            $notificationFr = 'a signalé votre publication';
            $notificationUr = 'reported your post';
            $notificationIn = 'reported your post';
            $body = 'Someone '.$notification;
            $bodyAr = $notificationAr.'أحدهم ';
            $bodyFr = 'Quelqu\'un '.$notificationFr;
        }
        if($data['type'] == 'approved'){
            $notification = 'Your post was approved';
            $notificationAr = 'تمت الموافقة على منشورك';
            $notificationFr = 'Votre publication a été approuvé';
            $notificationUr = 'Your post was approved';
            $notificationIn = 'Your post was approved';
            $body = $notification;
            $bodyAr = $notificationAr;
            $bodyFr = $notificationFr;
        }
        self::createNotification([
                        'toUserId' => $data['toUserId'],
                        'fromUserId' => $data['fromUserId'],
                        'userPostId' => $data['userPostId'],
                        'notification' => $notification,
                        'notificationAr' => $notificationAr,
                        'notificationFr' => $notificationFr,
                        'notificationUr' => $notificationUr,
                        'notificationIn' => $notificationIn,
                        'type' => $data['type'],
                    ]);
        $user = ReturnUserController::getUserById($data['toUserId']);
        if(!empty($user->deviceToken) && $user->isNotificationOn && $user->isLoggedIn){
            self::sendNotification([
                'type' => $data['type'],
                'userPostId' => $data['userPostId'],
                'deviceToken' => $user->deviceToken,
                'appLanguage' => $user->appLanguage,
                'title' => $title,
                'titleAr' => $titleAr,
                'titleFr' => $titleFr,
                'body' => $body,
                'bodyAr' => $bodyAr,
                'bodyFr' => $bodyFr,
            ]);  
        }
              
    }

    public static function createNotification($data){
        $postPotification = UserPostNotification::create([
            'toUserId' => $data['toUserId'], 
            'fromUserId' => $data['fromUserId'], 
            'userPostId' => $data['userPostId'], 
            'notification' => $data['notification'], 
            'notificationAr' => $data['notificationAr'], 
            'notificationFr' => $data['notificationFr'], 
            'notificationUr' => $data['notificationUr'], 
            'notificationIn' => $data['notificationIn'],
            'type' => $data['type'], 
        ]);
    }

    public static function sendNotification($data){  
        if($data['appLanguage'] == 'FR'){
            $title = $data['titleFr'];
            $body = $data['bodyFr'];
        } 
        else if($data['appLanguage'] == 'AR'){
            $title = $data['titleAr'];
            $body = $data['bodyAr'];
        } 
        else {
            $title = $data['title'];
            $body = $data['body'];
        }
        $sentData = [   
            'notificationType' => 'feed',
            'userPostId' => $data['userPostId'],
        ];
        FCMController::sendNotification($title, $body, $data['deviceToken'], $sentData);
    }
}