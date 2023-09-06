<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Feeds\PostCategoryController as PostCategoryController;
use App\Http\Controllers\API\Feeds\PostRulesController as PostRulesController;
use App\Http\Controllers\API\Feeds\PostReportController as PostReportController;
use Exception;

class FeedPostingConfigController extends BaseController 
{
    public function getFeedConfig(){
        try{
            $categories = PostCategoryController::getPostCategories();
            $rules = PostRulesController::getPostRules();
            $reports = PostReportController::getPostReports();
            return $this->sendResponse([
                'categories' => $categories,
                'rules' => $rules,
                'reports' => $reports,
            ], 'Feed config returned');
        } catch (Exception $e) {
            return $this->sendError('Exception', $e->getMessage(), 400);
        }
    }
}