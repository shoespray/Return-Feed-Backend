<?php

namespace App\Http\Controllers\API\Regions;

use App\Region;

class RegionController 
{  
    public static function getRegionById($id){
        return Region::where('id',$id)->first();
    }

    public static function getPostOrderNumber($regionId){
        $region = self::getRegionById($regionId);
        $region->postNumber = $region->postNumber + 1;
        $region->save();
        return $region->postNumber;
    }
}