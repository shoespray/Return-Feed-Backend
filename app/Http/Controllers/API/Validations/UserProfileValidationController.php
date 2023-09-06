<?php

namespace App\Http\Controllers\API\Validations;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\Validations\ValidationResponseController as ValidationResponseController;


class UserProfileValidationController
{
    public static function validateAddProfile(Request $request){
        $validator = Validator::make($request->all(), [
            'userName' => 'required|string|min:3|max:50',
        ]);
        return ValidationResponseController::validationResponse($validator);
    }

    public static function validateUpdateProfile(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'userName' => 'required|string|min:3|max:50',
        ]);
        return ValidationResponseController::validationResponse($validator);
    }
}