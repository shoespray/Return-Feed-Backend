<?php
namespace App\Http\Controllers\API\Feeds;

use App\UserPostLike;

class PostLikesController
{
    public static function likeOrUnlikePost($data){
        $likedPost = self::getLikedPostByUser([
            'userPostId' => $data['userPostId'],
            'likedByUserId' => $data['likedByUserId'],
        ]);
        if(empty($likedPost)){
            self::addLike($data);
            return ['isLiked' => true];
        }
        else {
            self::removeLike($likedPost);
            return ['isLiked' => false];
        }
    }
    public static function getLikedPostByUser($data){
        return UserPostLike::where(['userPostId' => $data['userPostId'], 'likedByUserId' => $data['likedByUserId']])->first();
    }

    public static function addLike($data){
        return UserPostLike::create([
            'userPostId' => $data['userPostId'], 
            'likedByUserId' => $data['likedByUserId'], 
        ]);
    }

    public static function removeLike($likedPost){
        $likedPost->delete();
    }
}