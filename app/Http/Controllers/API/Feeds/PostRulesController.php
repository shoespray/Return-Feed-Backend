<?php
namespace App\Http\Controllers\API\Feeds;

use App\PostRule;

class PostRulesController
{
    public static function getPostRules(){
        return PostRule::where('isActive', true)->orderBy('orderNumber')->get();
    }
}