<?php

namespace App\Models;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\Model;

class NotificationUser extends BaseModel
{
    protected $fillable = [
        'notification_id' => 'notification_id',
        'user_id' => 'user_id',
        'read_at' => 'read_at',
    ];

    protected $casts = [
        'notification_id' => 'integer',
        'user_id' => 'integer',
        'read_at' => 'datetime',
    ];


    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }


    //
}
