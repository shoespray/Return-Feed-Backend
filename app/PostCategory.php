<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostCategory extends Model
{
    use SoftDeletes;
    
    protected $softCascade  = [];

    protected $fillable = [
        'name', 'nameAr', 'nameFr', 'nameUr', 'nameIn', 'orderNumber', 'isActive',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $casts = [];
}
