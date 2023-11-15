<?php

namespace App\Http\Controllers\API\Feeds;

use App\Http\Controllers\API\FileController as FileController;
use App\UserPostMedia;

class PostMediaController
{
    public static function getTotalUploadedImages($userPostId){
        return UserPostMedia::where('userPostId', $userPostId)->count();
    }

    public static function uploadImages($data){
        $images = $data['images'];
        $i = 1;
        foreach($images as $image){
            $imageUrl = FileController::uploadFile($image['image_base64'], "feed/user-".$data['userId']."/post-".$data['userPostId'], $i++);
            self::addImage([
                'userPostId' => $data['userPostId'], 
                'mediaName' => $imageUrl, 
            ]);
        }         
    }

    public static function addImage($data){
        UserPostMedia::create([
            'userPostId' => $data['userPostId'], 
            'mediaName' => $data['mediaName'], 
            'mediaType' => 'image', 
        ]);
    }
    
    public static function checkIfImagesExist($imageIds){ 
        foreach($imageIds as $imageId){
            if(!UserPostMedia::where('id', $imageId)->exists()){
                return "Image does not exist";
            }
        }        
        return '';
    }

    public static function removePhotos($image_ids){
        foreach($image_ids as $image_id){
            UserPostMedia::where('id', $image_id)->delete();
        }
    }

}