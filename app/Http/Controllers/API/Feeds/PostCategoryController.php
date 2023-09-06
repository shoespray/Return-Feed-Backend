<?php
namespace App\Http\Controllers\API\Feeds;

use App\PostCategory;
use App\UserPostCategory;

class PostCategoryController
{
    public static function getPostCategories(){
        return PostCategory::where('isActive', true)->orderBy('orderNumber')->get();
    }

    public static function addPostCategory($data){
        return UserPostCategory::updateOrcreate([
            'userPostId' => $data['userPostId']
        ],
        [
            'postCategoryId' => $data['postCategoryId'],
        ]);
    }
}