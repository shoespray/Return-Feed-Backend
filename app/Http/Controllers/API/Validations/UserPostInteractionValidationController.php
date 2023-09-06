<?php

namespace App\Http\Controllers\API\Validations;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\Validations\ValidationResponseController as ValidationResponseController;


class UserPostInteractionValidationController
{
    public static function validateAddComment(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'comment' => 'required',
        ]);
        return ValidationResponseController::validationResponse($validator);
    }

    public static function validateReportPost(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'postReportId' => 'required',
        ]);
        return ValidationResponseController::validationResponse($validator);
    }
}