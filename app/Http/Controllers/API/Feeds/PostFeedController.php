<?php
namespace App\Http\Controllers\API\Feeds;

use App\Http\Controllers\API\Feeds\PostMediaController as PostMediaController;
use App\Http\Controllers\API\Feeds\PostCategoryController as PostCategoryController;
use App\UserPost;


class PostFeedController
{
    public static function getPostDetailed($id){
        return UserPost::where('id', $id)->with('profile')->with('community')->with('category')
                        ->with('media')->with('likes')
                        ->with(array('comments' => function ($query){
                            $query->with('profile');
                        }))
                        ->first();
    }

    public static function getPostById($id){
        return UserPost::where('id', $id)->first();
    }

    public static function getUserPostById($id, $userId){
        return UserPost::where(['id' => $id, 'userId' => $userId])->first();
    }

    public static function getAllPosts($regionId, $userId){
        return UserPost::where([
            'regionId' => $regionId, 'status' => 'approved', 
            ])            
            ->whereDoesntHave('reports', function ($query) use ($userId) {
                $query->where('reportedByUserId', '=', $userId);
            })
            ->with('profile')->with('community')->with('category')->with('media')->with('likes')
            ->with(array('comments' => function ($query){
                $query->with('profile');
            }))
            ->orderBy('created_at', 'desc')->paginate(25);
    }

    public static function getAllLatestPosts($lastUserPostId, $regionId){
        return UserPost::where('id', '>', $lastUserPostId)
            ->where([
            'regionId' => $regionId, 'status' => 'approved', 
            ])  
            ->with('profile')->with('community')->with('category')->with('media')
            ->with('likes')->with(array('comments' => function ($query){
                $query->with('profile');
            }))
            ->orderBy('created_at', 'desc')->paginate(25);
    }

    public static function getAllUserPosts($userId, $regionId){
        return UserPost::where([
            'regionId' => $regionId, 'userId' => $userId, 
            ])
            ->with('profile')->with('community')->with('category')->with('media')
            ->with('likes')->with(array('comments' => function ($query){
                $query->with('profile');
            }))
            ->orderBy('created_at', 'desc')->paginate(25);
    }

    public static function getPostsWithUserComments($userId, $regionId){
        return UserPost::where([
            'regionId' => $regionId, 'status' => 'approved', 
            ])
            ->whereHas('comments', function ($query) use ($userId) {
                $query->where('commentedByUserId', '=', $userId);
            })
            ->with('profile')->with('community')->with('category')->with('media')
            ->with('likes')->with(array('comments' => function ($query){
                $query->with('profile');
            }))
            ->orderBy('created_at', 'desc')->paginate(25);
    }

    public static function getPostsWithUserLikes($userId, $regionId){
        return UserPost::where([
            'regionId' => $regionId, 'status' => 'approved', 
            ])
            ->whereHas('likes', function ($query) use ($userId) {
                $query->where('likedByUserId', '=', $userId);
            })
            ->with('profile')->with('community')->with('category')->with('media')
            ->with('likes')->with(array('comments' => function ($query){
                $query->with('profile');
            }))
            ->orderBy('created_at', 'desc')->paginate(25);
    }

    public static function createPost($data){
        $post = UserPost::create([
            'userId' => $data['userId'], 
            'regionId' => $data['regionId'], 
            'postText' => $data['postText'], 
            'status' => 'approved', 
            'postNumber' => 1, 
        ]);
        if(!empty($post)){
            PostMediaController::uploadImages([
                'userId' => $data['userId'], 
                'userPostId' => $post->id,
                'images' => $data['images'],
            ]);            
            PostCategoryController::addPostCategory([
                'userPostId' => $post->id,
                'postCategoryId' => $data['postCategoryId'],
            ]);
            return self::getPostDetailed($post->id);
        }
        return '';
    }

    public static function updatePost($data){
        UserPost::where('id', $data['id'])
                ->update([
                    'postText' => $data['postText'], 
                ]);
        PostMediaController::uploadImages([
            'userId' => $data['userId'], 
            'userPostId' => $data['id'],
            'images' => $data['images'],
        ]);
        if(!empty($data['removedImageIds'])){
            PostMediaController::removePhotos($data['removedImageIds']);
        }
        PostCategoryController::addPostCategory([
            'userPostId' => $data['id'],
            'postCategoryId' => $data['postCategoryId'],
        ]);
        return self::getPostDetailed($data['id']);
    }

    public static function deletePost($id){
        $post = self::getPostById($id);
        $post->delete();
    }

    public static function updateNumberOfComments($id, $numberOfComments){
        $post = self::getPostById($id);
        $post->commentsNumber = $post->commentsNumber + $numberOfComments;
        $post->save(); 
    }

    public static function updateNumberOfLikes($id, $numberOfLikes){
        $post = self::getPostById($id);
        $post->likesNumber = $post->likesNumber + $numberOfLikes;
        $post->save(); 
    }

    public static function updateNumberOfReports($id){
        $post = self::getPostById($id);
        $post->reportsNumber += 1;      
        if($post->reportsNumber >= 3){
            $post->status = 'reported';
        }
        $post->save();
    }
}