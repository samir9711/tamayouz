<?php

namespace App\Models;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\Model;

class Ministry extends BaseModel
{
    protected $fillable = [
        'name' => 'name',
        'description' => 'description',
        'address' => 'address',
        'location' => 'location',
        'lat' => 'lat',
        'lon' => 'lon',
        'contact_phone' => 'contact_phone',
        'contact_email' => 'contact_email',
        'main_image' => 'main_image',
        'images' => 'images',
        'website' => 'website',
        'facebook' => 'facebook',
        'twitter' => 'twitter',
        'instagram' => 'instagram',
        'youtube' => 'youtube',
        'linkedin' => 'linkedin',
        'manager' => 'manager',
    ];

    protected $casts = [
        'lat' => 'float',
        'lon' => 'float',
        'images' => 'array',
    ];



    //
}
