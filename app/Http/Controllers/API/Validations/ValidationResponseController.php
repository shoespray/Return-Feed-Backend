<?php

namespace App\Http\Controllers\API\Validations;

class ValidationResponseController 
{
    public static function validationResponse($validator){
        if($validator->fails()){
            return ['isValid' => false, 'errorMessage' => $validator->errors()];     
        }
        return ['isValid' => true];
    }
}