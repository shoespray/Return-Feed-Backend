<?php
namespace App\Http\Controllers\API\Feeds;

use App\PostReport;
use App\UserPostReport;

class PostReportController
{
    public static function getPostReports(){
        return PostReport::where('isActive', true)->orderBy('orderNumber')->get();
    }

    public static function getReportedPostByUser($userPostId, $reportedByUserId){
        return UserPostReport::where(['userPostId' => $userPostId, 'reportedByUserId' => $reportedByUserId])->first();
    }

    public static function reportUserPost($data){
        return UserPostReport::updateOrCreate([
            'userPostId' => $data['userPostId'], 
            'reportedByUserId' => $data['reportedByUserId'], 
            'postReportId' => $data['postReportId'],
        ]);
    }
}