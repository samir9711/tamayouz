<?php

namespace App\Models;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\Model;

class AboutUs extends BaseModel
{protected $fillable = [
        'title' => 'title',
        'content' => 'content',
        'mission' => 'mission',
        'vision' => 'vision',
        'values' => 'values',
        'contact_email' => 'contact_email',
        'contact_phone' => 'contact_phone',
        'address' => 'address',
        'location' => 'location',
        'lat' => 'lat',
        'lon' => 'lon',
        'main_image' => 'main_image',
        'images' => 'images',
        'website' => 'website',
        'facebook' => 'facebook',
        'twitter' => 'twitter',
        'instagram' => 'instagram',
        'youtube' => 'youtube',
        'linkedin' => 'linkedin',
    ];

protected $casts = [
        'values' => 'array',
        'lat' => 'float',
        'lon' => 'float',
        'images' => 'array',
    ];

    //
}
