<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller as Controller;
use Exception;

class FileController extends Controller
{
    public static function uploadFile ($image, $dirName, $prefix = '') {
        $image = str_replace('data:image/jpeg;base64,', '', $image);
        $image = str_replace('data:image/jpg;base64,', '', $image);
        $image = str_replace('data:image/png;base64,', '', $image);
        $imageName = $prefix.time().'.'.'png';
        Storage::put($dirName.'/'.$imageName,  base64_decode($image), 'public');
        return $dirName.'/'.$imageName;
    }

    public static function downloadFile ($filename, $dirname){
       return response()->download(storage_path("app/public/{$dirname}/{$filename}"));
    }

}
