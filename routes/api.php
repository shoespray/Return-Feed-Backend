<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::prefix('v1')->group(function () {
    Route::middleware('auth:api')->group( function () {
        Route::middleware('throttle:40,1,default')->prefix('feed')->group(function () {
            Route::get('/config', 'API\FeedPostingConfigController@getFeedConfig');
            Route::get('/getAllPosts', 'API\UserFeedController@getAllFeedPosts');
            Route::get('/getLatestPosts/{lastUserPostId}', 'API\UserFeedController@getLatestFeedPosts');
        });

        Route::middleware('throttle:40,1,default')->prefix('feed/post')->group(function () {            
            Route::get('/getUserPosts', 'API\UserFeedController@getAllUserPosts');
            Route::get('/getUserPostDetails', 'API\UserFeedController@getAllUserPosts');
            Route::get('/getPost/{id}', 'API\UserFeedController@getPostById');
        });

        Route::middleware('throttle:30,1,default')->prefix('feed/post')->group(function () { 
            Route::post('/add', 'API\UserFeedController@addPost');
            Route::post('/update', 'API\UserFeedController@updatePost');
            Route::delete('/delete/{id}', 'API\UserFeedController@deletePost');
        });

        Route::middleware('throttle:40,1,default')->prefix('feed/post')->group(function () {
            Route::patch('/likeOrUnlike/{id}', 'API\UserPostInteractionController@likeOrUnlikePost');
            Route::post('/comment/add', 'API\UserPostInteractionController@addComment');
            Route::delete('/comment/delete/{id}', 'API\UserPostInteractionController@deleteComment');
            Route::post('/report', 'API\UserPostInteractionController@reportPost');
        });

        Route::middleware('throttle:40,1,default')->prefix('feed/notification')->group(function () {
            Route::get('/allNotifications', 'API\UserPostNotificationController@getAllUserNotifications');       
            Route::get('/allUnreadNotifications', 'API\UserPostNotificationController@getAllUnreadUserNotifications');       
            Route::patch('/markAsRead/{id}', 'API\UserPostNotificationController@markNotificationAsRead');       
            Route::patch('/markAllAsRead', 'API\UserPostNotificationController@markAllNotificationAsRead');       
        }); 

        Route::middleware('throttle:40,1,default')->prefix('user/profile')->group(function () {
            Route::post('/add', 'API\UserProfileController@addProfile');
            Route::post('/update', 'API\UserProfileController@updateProfile');
        });
    });
});