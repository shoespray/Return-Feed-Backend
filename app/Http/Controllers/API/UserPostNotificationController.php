<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Feeds\PostNotificationController as PostNotificationController;
use Illuminate\Http\Request;
use Exception;

class UserPostNotificationController extends BaseController 
{
    public function getAllUserNotifications(){
        try{
            $notifications = PostNotificationController::getAllNotifications([
                        'toUserId' => auth()->id(), 
                        'regionId' => auth()->user()->regionId, 
                        'status' => 'all',
                    ]);
            return $this->sendResponse($notifications, 'Notifications returned');
        } catch (Exception $e) {
            return $this->sendError('Exception', $e->getMessage(), 400);
        }
    }

    public function getAllUnreadUserNotifications(){
        try{
            $notifications = PostNotificationController::getAllNotifications([
                        'toUserId' => auth()->id(), 
                        'regionId' => auth()->user()->regionId, 
                        'status' => 'unRead',
                    ]);
            return $this->sendResponse($notifications, 'Notifications returned');
        } catch (Exception $e) {
            return $this->sendError('Exception', $e->getMessage(), 400);
        }
    }

    public function markNotificationAsRead($id){
        try{
            $notification = PostNotificationController::getNotificationById($id);
            if(empty($notification)){
                return $this->sendError('Notification does not exist','Notification does not exist', 400);
            }
            $notification = PostNotificationController::markAsRead($notification);
            return $this->sendResponse(['isRead' => $notification->isRead], 'Notification status returned');
        }
        catch (Exception $e) {
            return $this->sendError('Exception', $e->getMessage(), 400);
        }
    }

    public function markAllNotificationAsRead(){
        try{
            PostNotificationController::markAllAsRead(auth()->id(), auth()->user()->regionId);
            return $this->sendResponse(['isRead' => true], 'All notifications are marked as read');
        }
        catch (Exception $e) {
            return $this->sendError('Exception', $e->getMessage(), 400);
        }
    }

}