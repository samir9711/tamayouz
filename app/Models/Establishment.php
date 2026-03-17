<?php

namespace App\Models;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\Model;

class Establishment extends BaseModel
{
    protected $fillable = [
        'name' => 'name',
        'type' => 'type',
        'address' => 'address',
        'city' => 'city',
        'location' => 'location',
        'lat' => 'lat',
        'lon' => 'lon',
        'contact_phone' => 'contact_phone',
        'contact_email' => 'contact_email',
        'main_image' => 'main_image',
        'images' => 'images',
        'conditions' => 'conditions',
        'website' => 'website',
        'facebook' => 'facebook',
        'twitter' => 'twitter',
        'instagram' => 'instagram',
        'youtube' => 'youtube',
        'linkedin' => 'linkedin',
        'description'=>'description'

    ];

    protected $casts = [
        'lat' => 'float',
        'lon' => 'float',
        'images' => 'array',
        'conditions' => 'array',
    ];

    protected $search=['email', 'name'];


    public function accounts()
    {
        return $this->hasMany(EstablishmentAccount::class, 'establishment_id');
    }


    //
}
