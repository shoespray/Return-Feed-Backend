<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
// use Illuminate\Notifications\Notifiable;
// use Tymon\JWTAuth\Contracts\JWTSubject;

class ReturnUser extends Model implements AuthenticatableContract
{
    public function getAuthIdentifierName()
    {
        return 'id';
    }
    public function getAuthIdentifier()
    {
        return $this->id;
    }
    public function getAuthPassword()
    {
        return null;
    }
    public function getRememberToken()
    {
        return null;
    }
    public function setRememberToken($value) {}
    public function getRememberTokenName() {}

    public function region(){
        return $this->hasOne('App\Region', 'id', 'regionId');
    }

    public function country(){
        return $this->belongsTo('App\Country', 'countryId', 'id')
        ->select('id', 'code', 'name', 'nameAr', 'nameFr', 'nameUr', 'nameIn','hasCommunities', 'isTransferCreditsEnabled', 
                'hasBanner', 'hasInAppPayment', 'hasCashPayment', 'hasDebitCreditPayment', 'hasCollectedFromStores',
                'showUserPhoneNumber', 'hasNewBanner', 'instagramLink', 'facebookLink', 'youtubeLink', 'twitterLink', 'linkedInLink',
                'isStoreActive');
    }
}
