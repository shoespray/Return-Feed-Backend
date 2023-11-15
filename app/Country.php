<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use SoftDeletes;
    
    protected $softCascade  = [];

    protected $fillable = [
        'code', 'name', 'nameAr', 'nameFr', 'nameUr', 'nameIn', 'offlineUserCount', 'offlineUserAccCount', 'phoneCode', 
        'currency', 'hasCommunities', 'isActive', 'orderNumber', 'flagImage', 'isTransferCreditsEnabled', 'isFriendReferralEnabled',
        'hasBanner', 'hasInAppPayment', 'hasCashPayment', 'hasDebitCreditPayment', 'hasCollectedFromStores', 'showUserPhoneNumber',
        'hasNewBanner', 'instagramLink', 'facebookLink', 'youtubeLink', 'twitterLink', 'linkedInLink', 'isStoreActive',
    ];

    protected $hidden = ['deleted_at', 'created_at', 'updated_at'];

    protected $casts = [];
}
