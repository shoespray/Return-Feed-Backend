<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;

class Region extends Model
{
    use SoftDeletes, PostgisTrait;

    protected $softCascade  = [];
    
    protected $fillable = [
        'code', 'name', 'nameAr', 'nameFr', 'nameUr', 'nameIn', 'countryId', 'geolocation', 'geolocationDelta', 'isDeliveryAvailable', 'minNbrOfBags',
        'deliveryOrderNumber', 'hasInspectionModule', 'bagOrderNumber', 'hasSmartBins', 'accountNumber', 'isSmartBinMapShown', 'hasSliders',
        'hasDropOffSchedule', 'isActive', 'canRequestBags', 'canAssignBags', 'showUserQrCode', 'hasPaymentByUserCredits',
        'hasFeed',
    ];

    protected $postgisFields = [
        'geolocation',
        'geolocationDelta',
    ];

    protected $postgisTypes = [
        'geolocation' => [
            'geomtype' => 'geography',
            'srid' => 4326
        ],
        'geolocationDelta' => [
            'geomtype' => 'geography',
            'srid' => 4326
        ],
    ];
    
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $casts = [];
}
