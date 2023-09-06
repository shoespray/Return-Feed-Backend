<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Validations\UserPostInteractionValidationController as UserPostInteractionValidationController;
use App\Http\Controllers\API\ReturnUsers\ReturnUserProfileController as ReturnUserProfileController;
use App\Http\Controllers\API\Feeds\PostFeedController as PostFeedController;
use App\Http\Controllers\API\Feeds\PostCommentController as PostCommentController;
use App\Http\Controllers\API\Feeds\PostLikesController as PostLikesController;
use App\Http\Controllers\API\Feeds\PostReportController as PostReportController;
use App\Http\Controllers\API\Feeds\PostNotificationController as PostNotificationController;
use Illuminate\Http\Request;
use Exception;

class UserPostInteractionController extends BaseController 
{
    public function addComment(Request $request){
        try{
            $validator = UserPostInteractionValidationController::validateAddComment($request);
            if(!$validator['isValid']){
                return $this->sendError('Validation Error: '.$validator['errorMessage'],'Validation Error.'); 
            } 
            if(empty(ReturnUserProfileController::getUserProfile(auth()->id()))){
                return $this->sendError('Create a profile before commenting','Create a profile before commenting', 400);
            }
            $post = PostFeedController::getPostById($request->id);
            if(empty($post)){
                return $this->sendError('Post does not exist','Post does not exist', 400);
            }
            $comment = PostCommentController::addComment([
                        'userPostId' => $request->id, 
                        'commentedByUserId' => auth()->id(), 
                        'comment' => $request->comment, 
                    ]);
            if(!empty($comment)){
                PostFeedController::updateNumberOfComments($request->id, 1);
                if($post->userId != auth()->id()){
                    PostNotificationController::addPostNotification([
                        'type' => 'commented',
                        'toUserId' => $post->userId,
                        'fromUserId' => auth()->id(),
                        'userPostId' => $request->id,
                    ]);
                }
            }
            return $this->sendResponse($comment, 'comment added');
        } catch (Exception $e) {
            return $this->sendError('Exception', $e->getMessage(), 400);
        }
    }

    public function deleteComment($id){
        try{
            $comment = PostCommentController::getCommentById($id);
            $userPostId = $comment->userPostId;
            if(empty($comment)){
                return $this->sendError('Comment does not exist','Comment does not exist', 400);
            }
            if($comment->commentedByUserId != auth()->id()){
                return $this->sendError('Could not delete comment','Could not delete comment', 400);
            }
            PostCommentController::deleteComment($comment);
            PostFeedController::updateNumberOfComments($userPostId, -1);
            return $this->sendResponse(['isDeleted' =>  true], 'Comment deleted');
        } catch (Exception $e) {
            return $this->sendError('Exception', $e->getMessage(), 400);
        }
    }

    public function likeOrUnlikePost($id){
        try{
            if(empty(ReturnUserProfileController::getUserProfile(auth()->id()))){
                return $this->sendError('Create a profile before liking the post','Create a profile before liking the post', 400);
            }
            $post = PostFeedController::getPostById($id);
            if(empty($post)){
                return $this->sendError('Post does not exist','Post does not exist', 400);
            }
            $response = PostLikesController::likeOrUnlikePost([
                            'userPostId' => $id,
                            'likedByUserId' => auth()->id(),
                        ]);
            if($response['isLiked']){
                PostFeedController::updateNumberOfLikes($id, 1);
                $likedNotification = PostNotificationController::getLikedNotificationByUser(auth()->id(), $id);
                if($post->userId != auth()->id() && empty($likedNotification)){
                    PostNotificationController::addPostNotification([
                        'type' => 'liked',
                        'toUserId' => $post->userId,
                        'fromUserId' => auth()->id(),
                        'userPostId' => $id,
                    ]);
                }                
                $message = 'Liked post';
            }
            else {
                PostFeedController::updateNumberOfLikes($id, -1);
                $message = 'Unliked post';
            }
            return $this->sendResponse($response, $message);
        } catch (Exception $e) {
            return $this->sendError('Exception', $e->getMessage(), 400);
        }
    }

    public function reportPost(Request $request){
        try{
            $validator = UserPostInteractionValidationController::validateReportPost($request);
            if(!$validator['isValid']){
                return $this->sendError('Validation Error: '.$validator['errorMessage'],'Validation Error.'); 
            } 
            if(empty(ReturnUserProfileController::getUserProfile(auth()->id()))){
                return $this->sendError('Create a profile before reporting the post','Create a profile before reporting the post', 400);
            }
            $post = PostFeedController::getPostById($request->id);
            if(empty($post)){
                return $this->sendError('Post does not exist','Post does not exist', 400);
            }
            if(!empty($post) && $post->userId == auth()->id()){
                return $this->sendError('You cannot report your own post','You cannot report your own post', 400);
            }
            if(!empty(PostReportController::getReportedPostByUser($request->id, auth()->id()))){
                return $this->sendError('You have already reported this post','You have already reported this post', 400);
            }
            $report = PostReportController::reportUserPost([
                        'userPostId' => $request->id, 
                        'reportedByUserId' => auth()->id(), 
                        'postReportId' => $request->postReportId, 
                    ]);
            if(!empty($report)){
                PostFeedController::updateNumberOfReports($request->id);
                PostNotificationController::addPostNotification([
                    'type' => 'reported',
                    'toUserId' => $post->userId,
                    'fromUserId' => auth()->id(),
                    'userPostId' => $request->id,
                ]);
            }
            return $this->sendResponse($report, 'Post reported');
        } catch (Exception $e) {
            return $this->sendError('Exception', $e->getMessage(), 400);
        }
    }


}