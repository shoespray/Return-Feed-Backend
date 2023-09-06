<?php

namespace App\Http\Controllers\API\Validations;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\Validations\ValidationResponseController as ValidationResponseController;


class UserPostValidationController
{
    public static function validateAddPost(Request $request){
        $validator = Validator::make($request->all(), [
            'postCategoryId' => 'required',
            'postText' => 'required',
        ]);
        return ValidationResponseController::validationResponse($validator);
    }

    public static function validateUpdatePost(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'postCategoryId' => 'required',
            'postText' => 'required',
        ]);
        return ValidationResponseController::validationResponse($validator);
    }
}