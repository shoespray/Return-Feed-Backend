<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Validations\UserPostValidationController as UserPostValidationController;
use App\Http\Controllers\API\ReturnUsers\ReturnUserProfileController as ReturnUserProfileController;
use App\Http\Controllers\API\Feeds\PostFeedController as PostFeedController;
use App\Http\Controllers\API\Feeds\PostMediaController as PostMediaController;
use App\Http\Controllers\API\Feeds\PostNotificationController as PostNotificationController;
use App\Http\Controllers\API\Feeds\PostCommentController as PostCommentController;
use App\Http\Controllers\API\Feeds\PostLikesController as PostLikesController;
use Illuminate\Http\Request;
use Exception;

class UserFeedController extends BaseController 
{
    public function getAllFeedPosts(){
        try{
            $posts = PostFeedController::getAllPosts(auth()->user()->regionId, auth()->id());
            return $this->sendResponse($posts, 'Post returned');
        } catch (Exception $e) {
            return $this->sendError('Exception', $e->getMessage(), 400);
        }
    }

    public function getLatestFeedPosts($lastUserPostId){
        try{
            if(empty($lastUserPostId))
                $posts = PostFeedController::getAllPosts(auth()->user()->regionId, auth()->id());
            else 
                $posts = PostFeedController::getAllLatestPosts($lastUserPostId, auth()->user()->regionId);
            return $this->sendResponse($posts, 'Post returned');
        } catch (Exception $e) {
            return $this->sendError('Exception', $e->getMessage(), 400);
        }
    }

    public function getAllUserPosts(){
        try{
            $posts = PostFeedController::getAllUserPosts( auth()->id(), auth()->user()->regionId);
            $comments = PostFeedController::getPostsWithUserComments( auth()->id(), auth()->user()->regionId);
            $likes = PostFeedController::getPostsWithUserLikes( auth()->id(), auth()->user()->regionId);
            return $this->sendResponse([
                'myPosts' => $posts,
                'myLikes' => $likes,
                'myComments' => $comments,
            ], 'Post returned');
        } catch (Exception $e) {
            return $this->sendError('Exception', $e->getMessage(), 400);
        }
    }

    public function getPostById($id){
        try{
            $post = PostFeedController::getPostDetailed($id);
            return $this->sendResponse($post, 'Post returned');
        } catch (Exception $e) {
            return $this->sendError('Exception', $e->getMessage(), 400);
        }
    }

    public function addPost(Request $request){
        try{
            $validator = UserPostValidationController::validateAddPost($request);
            if(!$validator['isValid']){
                return $this->sendError('Validation Error: '.$validator['errorMessage'],'Validation Error.'); 
            } 
            if(empty(ReturnUserProfileController::getUserProfile(auth()->id()))){
                return $this->sendError('Create a profile before posting','Create a profile before posting', 400);
            }
            if(!empty($request->images) && count($request->images) > 3){
                return $this->sendError('You can post up to 3 images','You can post up to 3 images', 400);
            }
            $post = PostFeedController::createPost([
                        'userId' => auth()->id(), 
                        'regionId' => auth()->user()->regionId, 
                        'postText' => $request->postText, 
                        'postCategoryId' => $request->postCategoryId, 
                        'images' => $request->images,
                    ]);
            //to be commented
            if(!empty($post)){
                PostNotificationController::addPostNotification([
                    'type' => 'approved',
                    'toUserId' => auth()->id(),
                    'fromUserId' => NULL,
                    'userPostId' => $post->id,
                ]);
            }
            return $this->sendResponse($post, 'Post created');
        } catch (Exception $e) {
            return $this->sendError('Exception', $e->getMessage(), 400);
        }
    }

    public function updatePost(Request $request){
        try{
            $validator = UserPostValidationController::validateUpdatePost($request);
            if(!$validator['isValid']){
                return $this->sendError('Validation Error: '.$validator['errorMessage'],'Validation Error.'); 
            } 
            if(empty(ReturnUserProfileController::getUserProfile(auth()->id()))){
                return $this->sendError('Create a profile before posting','Create a profile before posting', 400);
            }
            if(empty(PostFeedController::getUserPostById($request->id, auth()->id()))){
                return $this->sendError('Post does not exist','Post does not exist', 400);
            }

            $removedImageIds = array();
            if(!empty($request->removedImageIds)){
                $removedImageIds = explode(',', $request->removedImageIds);
            }
            $totalUploadedImages = PostMediaController::getTotalUploadedImages($request->id); 
            $totalImages = count($request->images) + $totalUploadedImages - count($removedImageIds);
            if(!empty($request->images) && count($request->images) > 3){
                return $this->sendError('You can post up to 3 images','You can post up to 3 images', 400);
            }
            $post = PostFeedController::updatePost([
                        'id' => $request->id, 
                        'userId' => auth()->id(), 
                        'postText' => $request->postText, 
                        'postCategoryId' => $request->postCategoryId, 
                        'images' => $request->images,
                        'removedImageIds' => $removedImageIds,
                    ]);
            return $this->sendResponse($post, 'Post created');
        } catch (Exception $e) {
            return $this->sendError('Exception', $e->getMessage(), 400);
        }
    }

    public function deletePost($id){
        try{
            if(empty(PostFeedController::getUserPostById($id, auth()->id()))){
                return $this->sendError('Post does not exist','Post does not exist', 400);
            }
            PostFeedController::deletePost($id);
            return $this->sendResponse(['isDeleted' =>  true], 'Post deleted');
        } catch (Exception $e) {
            return $this->sendError('Exception', $e->getMessage(), 400);
        }
    }
}