<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostRule extends Model
{
    use SoftDeletes;
    
    protected $softCascade  = [];

    protected $fillable = [
        'name', 'nameAr', 'nameFr', 'nameUr', 'nameIn', 'description', 'descriptionAr', 'descriptionFr', 'descriptionUr', 'descriptionIn', 
        'orderNumber', 'isActive',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $casts = [];
}
