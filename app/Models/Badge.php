<?php

namespace App\Models;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\Model;

class Badge extends BaseModel
{protected $fillable = [
        'code' => 'code',
        'ministry_id' => 'ministry_id',
        'user_id' => 'user_id',
        'title' => 'title',
        'description' => 'description',
    ];

protected $casts = [
        'ministry_id' => 'integer',
        'user_id' => 'integer',
    ];

    //
    public function discounts()
    {
        return $this->hasMany(BadgeDiscount::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ministry()
    {
        return $this->belongsTo(Ministry::class);
    }

}
