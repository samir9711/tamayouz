<?php

namespace App\Models;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\Model;

class EstablishmentAccount extends BaseAuthModel
{
    protected $fillable = [
        'establishment_id' => 'establishment_id',
        'name' => 'name',
        'email' => 'email',
        'password' => 'password',
        'role' => 'role',
    ];

    protected $casts = [
        'establishment_id' => 'integer',
    ];

    //
}
