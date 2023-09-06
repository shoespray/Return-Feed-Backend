<?php
namespace App\Http\Controllers\API\Feeds;

use App\UserPostComment;

class PostCommentController
{
    public static function addComment($data){
        return UserPostComment::create([
            'userPostId' => $data['userPostId'], 
            'commentedByUserId' => $data['commentedByUserId'],  
            'comment' => $data['comment'], 
        ]);
    }

    public static function getCommentById($id){
        return UserPostComment::where('id', $id)->first();
    }

    public static function deleteComment($comment){
        $comment->delete();
    }
}